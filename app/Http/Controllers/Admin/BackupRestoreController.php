<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupRestoreController extends Controller
{
    /**
     * Display the backup & restore page.
     */
    public function index(): View
    {
        $backupDir = storage_path('backups');
        $backups = [];

        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && str_ends_with($file, '.sql')) {
                    $filePath = $backupDir.DIRECTORY_SEPARATOR.$file;
                    $backups[] = [
                        'filename' => $file,
                        'size' => $this->formatBytes(filesize($filePath)),
                        'created_at' => filemtime($filePath) ? date('Y-m-d H:i:s', filemtime($filePath)) : 'Unknown',
                    ];
                }
            }
            // Sort by modified time descending (newest first)
            usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));
        }

        $hasOlderBackups = count($backups) > 5;
        $backups = array_slice($backups, 0, 5);

        return view('admin.backup-restore.index', compact('backups', 'hasOlderBackups'));
    }

    /**
     * Helper to format bytes to human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Run mysqldump and stream the result directly as a file download.
     * The user's browser save-dialog will appear so they can choose where to save it.
     */
    public function backup(Request $request): BinaryFileResponse|RedirectResponse|JsonResponse
    {
        $now = now();
        $filename = $now->format('F j Y g-i A').'.sql'; // e.g. "July 12 2026 7-34 PM.sql"

        $backupDir = storage_path('backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $serverFilePath = $backupDir.DIRECTORY_SEPARATOR.$filename;

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $mysqldump = $this->resolveBinary('mysqldump');
        $passwordArg = $password ? '-p'.escapeshellarg($password) : '';

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($mysqldump),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($serverFilePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            @unlink($serverFilePath);
            $errorDetail = implode(' ', $output);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Backup failed: '.$errorDetail,
                ], 500);
            }

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup failed: '.$errorDetail);
        }

        ActivityLog::log(
            'DATABASE_BACKUP',
            'Database backup created and saved: '.$filename
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'filename' => $filename,
            ]);
        }

        return response()->download($serverFilePath, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    /**
     * Restore the database from an uploaded SQL file.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'mimes:sql,txt', 'max:102400'],
        ]);

        $file = $request->file('backup_file');

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $mysql = $this->resolveBinary('mysql');
        $passwordArg = $password ? '-p'.escapeshellarg($password) : '';

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
            escapeshellarg($mysql),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($file->getPathname())
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $errorDetail = implode(' ', $output);

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Restore failed: '.$errorDetail);
        }

        ActivityLog::log(
            'DATABASE_RESTORE',
            'Database restored from uploaded file: '.$file->getClientOriginalName()
        );

        return redirect()
            ->route('admin.backup-restore')
            ->with('success', 'Database restored successfully from "'.$file->getClientOriginalName().'".');
    }

    /**
     * Restore the database from a backup file already in server's storage/backups/ directory.
     */
    public function restoreLocal(Request $request): RedirectResponse
    {
        $request->validate([
            'filename' => ['required', 'string'],
        ]);

        $filename = $request->input('filename');

        if (! preg_match('/^[a-zA-Z0-9_\-\s\.]+\.sql$/', $filename)) {
            abort(400, 'Invalid backup filename.');
        }

        $filePath = storage_path('backups/'.$filename);

        if (! file_exists($filePath)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup file not found on server.');
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $mysql = $this->resolveBinary('mysql');
        $passwordArg = $password ? '-p'.escapeshellarg($password) : '';

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
            escapeshellarg($mysql),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $errorDetail = implode(' ', $output);

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Restore failed: '.$errorDetail);
        }

        ActivityLog::log(
            'DATABASE_RESTORE',
            'Database restored from server backup file: '.$filename
        );

        return redirect()
            ->route('admin.backup-restore')
            ->with('success', 'Database restored successfully from server backup "'.$filename.'".');
    }

    /**
     * Download a specific backup file from the server's storage/backups/ directory.
     */
    public function downloadLocal(string $filename): BinaryFileResponse|RedirectResponse
    {
        if (! preg_match('/^[a-zA-Z0-9_\-\s\.]+\.sql$/', $filename)) {
            abort(400, 'Invalid backup filename.');
        }

        $filePath = storage_path('backups/'.$filename);

        if (! file_exists($filePath)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup file not found on server.');
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    /**
     * Delete a specific backup file from the server's storage/backups/ directory.
     */
    public function deleteLocal(string $filename): RedirectResponse
    {
        if (! preg_match('/^[a-zA-Z0-9_\-\s\.]+\.sql$/', $filename)) {
            abort(400, 'Invalid backup filename.');
        }

        $filePath = storage_path('backups/'.$filename);

        if (! file_exists($filePath)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup file not found on server.');
        }

        @unlink($filePath);

        ActivityLog::log(
            'DATABASE_BACKUP_DELETE',
            'Deleted server backup file: '.$filename
        );

        return redirect()
            ->route('admin.backup-restore')
            ->with('success', 'Backup file "'.$filename.'" deleted from server storage.');
    }

    /**
     * Resolve the full path to a MySQL binary (mysqldump or mysql),
     * falling back to common installation paths when not on system PATH.
     *
     * @param  'mysqldump'|'mysql'  $binary
     */
    private function resolveBinary(string $binary): string
    {
        $candidates = [
            // XAMPP (Windows)
            "C:\\xampp\\mysql\\bin\\{$binary}.exe",
            // WAMP64
            ...glob("C:\\wamp64\\bin\\mysql\\*\\bin\\{$binary}.exe") ?: [],
            // Laragon
            ...glob("C:\\laragon\\bin\\mysql\\*\\bin\\{$binary}.exe") ?: [],
            // MAMP (Windows)
            "C:\\MAMP\\bin\\mysql\\bin\\{$binary}.exe",
            // Linux/macOS common paths
            "/usr/bin/{$binary}",
            "/usr/local/bin/{$binary}",
            "/opt/homebrew/bin/{$binary}",
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return $binary;
    }
}
