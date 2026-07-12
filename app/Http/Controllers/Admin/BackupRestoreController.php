<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupRestoreController extends Controller
{
    /**
     * Display the backup & restore page.
     */
    public function index(): View
    {
        return view('admin.backup-restore.index');
    }

    /**
     * Run mysqldump and stream the result directly as a file download.
     * The user's browser save-dialog will appear so they can choose where to save it.
     */
    public function backup(): StreamedResponse|RedirectResponse
    {
        $now = now();
        $filename = $now->format('F j Y g-i A').'.sql'; // e.g. "July 12 2026 7-34 PM.sql"

        $tmpFile = tempnam(sys_get_temp_dir(), 'db_backup_');

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
            escapeshellarg($tmpFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            @unlink($tmpFile);
            $errorDetail = implode(' ', $output);

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup failed: '.$errorDetail);
        }

        return response()->streamDownload(function () use ($tmpFile): void {
            $handle = fopen($tmpFile, 'rb');
            while (! feof($handle)) {
                echo fread($handle, 8192);
            }
            fclose($handle);
            @unlink($tmpFile);
        }, $filename, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
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

        return redirect()
            ->route('admin.backup-restore')
            ->with('success', 'Database restored successfully from "'.$file->getClientOriginalName().'".');
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
