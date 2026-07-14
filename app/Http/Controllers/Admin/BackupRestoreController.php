<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\BackupSettingsService;
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
        $settings = BackupSettingsService::get();
        $backupDir = $settings['folder'] ?? storage_path('backups');
        $backups = $this->listBackupFiles($backupDir);

        $hasOlderBackups = count($backups) > 5;
        $backups = array_slice($backups, 0, 5);

        $folderPresets = [
            [
                'label' => 'Default Backups',
                'path' => storage_path('backups'),
                'icon' => 'fa-folder',
            ],
            [
                'label' => 'Storage App',
                'path' => storage_path('app'),
                'icon' => 'fa-box-archive',
            ],
            [
                'label' => 'Storage Root',
                'path' => storage_path(),
                'icon' => 'fa-hard-drive',
            ],
        ];

        return view('admin.backup-restore.index', compact(
            'backups',
            'hasOlderBackups',
            'settings',
            'folderPresets',
            'backupDir',
        ));
    }

    /**
     * List subdirectories within allowed backup folder roots.
     */
    public function listFolders(Request $request): JsonResponse
    {
        $path = $request->query('path', storage_path('backups'));

        if (! is_string($path) || ! $this->isAllowedBackupPath($path)) {
            return response()->json([
                'success' => false,
                'error' => 'Access to this directory is not allowed.',
            ], 403);
        }

        if (! is_dir($path)) {
            return response()->json([
                'success' => false,
                'error' => 'Directory does not exist.',
            ], 404);
        }

        $folders = [];
        $entries = @scandir($path) ?: [];

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$entry;

            if (is_dir($fullPath)) {
                $folders[] = [
                    'name' => $entry,
                    'path' => $fullPath,
                ];
            }
        }

        usort($folders, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return response()->json([
            'success' => true,
            'current' => $path,
            'parent' => $this->parentAllowedPath($path),
            'folders' => $folders,
            'breadcrumbs' => $this->buildBreadcrumbs($path),
        ]);
    }

    /**
     * @return list<array{filename: string, size: string, created_at: string}>
     */
    private function listBackupFiles(string $backupDir): array
    {
        $backups = [];

        if (! is_dir($backupDir)) {
            return $backups;
        }

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

        usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    private function isAllowedBackupPath(string $path): bool
    {
        $realPath = realpath($path);

        if ($realPath === false) {
            $parent = dirname($path);
            $realParent = realpath($parent);

            if ($realParent === false) {
                return false;
            }

            $realPath = $realParent.DIRECTORY_SEPARATOR.basename($path);
        }

        $allowedRoots = array_filter([
            realpath(storage_path()),
            realpath(base_path('storage')),
        ]);

        foreach ($allowedRoots as $root) {
            if ($root !== false && str_starts_with($realPath, $root)) {
                return true;
            }
        }

        return false;
    }

    private function parentAllowedPath(string $path): ?string
    {
        $parent = dirname($path);
        $storageRoot = realpath(storage_path());

        if ($storageRoot === false) {
            return null;
        }

        if ($parent === $path || ! $this->isAllowedBackupPath($parent)) {
            return null;
        }

        return $parent;
    }

    /**
     * @return list<array{label: string, path: string}>
     */
    private function buildBreadcrumbs(string $path): array
    {
        $storageRoot = realpath(storage_path());

        if ($storageRoot === false) {
            return [['label' => basename($path), 'path' => $path]];
        }

        $breadcrumbs = [];
        $current = realpath($path) ?: $path;

        while ($current && str_starts_with($current, $storageRoot)) {
            $breadcrumbs[] = [
                'label' => basename($current) ?: 'storage',
                'path' => $current,
            ];

            $parent = dirname($current);

            if ($parent === $current) {
                break;
            }

            $current = $parent;
        }

        return array_reverse($breadcrumbs);
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

        $backupDir = BackupSettingsService::get()['folder'] ?? storage_path('backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $serverFilePath = $backupDir.DIRECTORY_SEPARATOR.$filename;

        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath === ':memory:') {
                file_put_contents($serverFilePath, '-- sqlite memory backup');
            } else {
                if (! file_exists($dbPath) || ! @copy($dbPath, $serverFilePath)) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Backup failed: SQLite database file not found or not copyable.',
                        ], 500);
                    }

                    return redirect()
                        ->route('admin.backup-restore')
                        ->with('error', 'Backup failed: SQLite database file not found or not copyable.');
                }
            }
        } else {
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

        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath !== ':memory:') {
                @copy($file->getPathname(), $dbPath);
            }
        } else {
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

        $backupDir = BackupSettingsService::get()['folder'] ?? storage_path('backups');
        $filePath = rtrim($backupDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;

        if (! file_exists($filePath)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup file not found on server.');
        }

        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath !== ':memory:') {
                @copy($filePath, $dbPath);
            }
        } else {
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

        $backupDir = BackupSettingsService::get()['folder'] ?? storage_path('backups');
        $filePath = rtrim($backupDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;

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

        $backupDir = BackupSettingsService::get()['folder'] ?? storage_path('backups');
        $filePath = rtrim($backupDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;

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
     * Update backup settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->merge([
            'enabled' => $request->has('enabled') ? '1' : '0',
        ]);

        $request->validate([
            'enabled' => ['required', 'boolean'],
            'time' => ['required', 'string', 'regex:/^(?:[0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'folder' => ['required', 'string'],
        ]);

        $folder = $request->input('folder');

        // Check if directory exists or can be created, and is writable
        if (! is_dir($folder)) {
            if (! @mkdir($folder, 0755, true)) {
                return redirect()
                    ->route('admin.backup-restore')
                    ->with('error', "The selected folder directory does not exist and could not be created: {$folder}");
            }
        }

        if (! is_writable($folder)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', "The selected folder directory is not writable: {$folder}");
        }

        BackupSettingsService::set([
            'enabled' => (bool) $request->input('enabled'),
            'time' => $request->input('time'),
            'folder' => $folder,
        ]);

        ActivityLog::log(
            'SYSTEM_SETTINGS',
            'Automatic database backup settings updated. Status: '.($request->input('enabled') ? 'Enabled' : 'Disabled')
        );

        return redirect()
            ->route('admin.backup-restore')
            ->with('success', 'Automatic backup settings saved successfully.');
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
