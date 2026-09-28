<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\BackupSettingsService;
use App\Services\BackupStorageService;
use App\Services\DatabaseDumpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class BackupRestoreController extends Controller
{
    public function __construct(
        protected BackupStorageService $backupStorage
    ) {}

    /**
     * Display the backup & restore page.
     */
    public function index(): View
    {
        $settings = BackupSettingsService::get();
        $backupDir = $settings['folder'] ?? storage_path('backups');
        $backups = $this->backupStorage->list();

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
     * Run mysqldump and stream the result directly as a file download.
     */
    public function backup(Request $request): BinaryFileResponse|RedirectResponse|JsonResponse|StreamedResponse
    {
        $now = now();
        $sqlFilename = $now->format('F j Y g-i A').'.sql';
        $zipFilename = $now->format('F j Y g-i A').'.zip';

        $tempDir = storage_path('app/temp_backup_'.uniqid());
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempSqlFilePath = $tempDir.DIRECTORY_SEPARATOR.$sqlFilename;
        $tempZipFilePath = $tempDir.DIRECTORY_SEPARATOR.$zipFilename;

        if (! DatabaseDumpService::dump($tempSqlFilePath)) {
            $this->cleanTempDir($tempDir);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Backup failed: Unable to export database tables.',
                ], 500);
            }

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup failed: Unable to export database tables.');
        }

        $finalFilePath = $tempSqlFilePath;
        $finalFilename = $sqlFilename;

        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive;
            if ($zip->open($tempZipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFile($tempSqlFilePath, $sqlFilename);
                $zip->close();
                @unlink($tempSqlFilePath);
                $finalFilePath = $tempZipFilePath;
                $finalFilename = $zipFilename;
            }
        }

        // Store to configured backup disk
        $stored = $this->backupStorage->store($finalFilePath, $finalFilename);
        $this->cleanTempDir($tempDir);

        if (! $stored) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Backup failed: Unable to persist backup to storage disk.',
                ], 500);
            }

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup failed: Unable to persist backup to storage disk.');
        }

        ActivityLog::log(
            'DATABASE_BACKUP',
            'Database backup created and saved: '.$finalFilename
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'filename' => $finalFilename,
            ]);
        }

        return $this->backupStorage->download($finalFilename);
    }

    /**
     * Restore the database from an uploaded SQL or ZIP file.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'mimes:sql,txt,zip', 'max:102400'],
        ]);

        $file = $request->file('backup_file');
        $filePath = $file->getPathname();
        $isZip = $file->getClientOriginalExtension() === 'zip' || $file->getMimeType() === 'application/zip';
        $tempExtractDir = null;

        if ($isZip) {
            $zip = new ZipArchive;
            if ($zip->open($filePath) === true) {
                $sqlFilenameInZip = null;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (str_ends_with(strtolower($name), '.sql')) {
                        $sqlFilenameInZip = $name;
                        break;
                    }
                }

                if ($sqlFilenameInZip) {
                    $tempExtractDir = storage_path('app/temp_restore_'.uniqid());
                    if (! is_dir($tempExtractDir)) {
                        mkdir($tempExtractDir, 0755, true);
                    }
                    $zip->extractTo($tempExtractDir, $sqlFilenameInZip);
                    $zip->close();
                    $filePath = $tempExtractDir.DIRECTORY_SEPARATOR.$sqlFilenameInZip;
                } else {
                    $zip->close();

                    return redirect()
                        ->route('admin.backup-restore')
                        ->with('error', 'Restore failed: No .sql file found in the uploaded ZIP archive.');
                }
            } else {
                return redirect()
                    ->route('admin.backup-restore')
                    ->with('error', 'Restore failed: Unable to open ZIP file.');
            }
        }

        try {
            $this->createSafetyBackup();
        } catch (\Exception $e) {
            if ($tempExtractDir) {
                $this->cleanTempDir($tempExtractDir);
            }

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Restore aborted: Failed to create safety backup. Error: '.$e->getMessage());
        }

        if (! DatabaseDumpService::restore($filePath)) {
            if ($tempExtractDir) {
                $this->cleanTempDir($tempExtractDir);
            }

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Restore failed: Unable to import database tables.');
        }

        if ($tempExtractDir) {
            $this->cleanTempDir($tempExtractDir);
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
     * Restore the database from a backup file already on the configured backup disk.
     */
    public function restoreLocal(Request $request): RedirectResponse
    {
        $request->validate([
            'filename' => ['required', 'string'],
        ]);

        $filename = $request->input('filename');

        if (! $this->backupStorage->isBackupFile($filename)) {
            abort(400, 'Invalid backup filename.');
        }

        if (! $this->backupStorage->exists($filename)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup file not found on server.');
        }

        $localPath = $this->backupStorage->getLocalCopy($filename);
        if (! $localPath || ! file_exists($localPath)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Failed to retrieve backup file from storage.');
        }

        $isZip = str_ends_with(strtolower($filename), '.zip');
        $tempExtractDir = null;
        $restoreSqlPath = $localPath;

        if ($isZip) {
            $zip = new ZipArchive;
            if ($zip->open($localPath) === true) {
                $sqlFilenameInZip = null;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (str_ends_with(strtolower($name), '.sql')) {
                        $sqlFilenameInZip = $name;
                        break;
                    }
                }

                if ($sqlFilenameInZip) {
                    $tempExtractDir = storage_path('app/temp_restore_'.uniqid());
                    if (! is_dir($tempExtractDir)) {
                        mkdir($tempExtractDir, 0755, true);
                    }
                    $zip->extractTo($tempExtractDir, $sqlFilenameInZip);
                    $zip->close();
                    $restoreSqlPath = $tempExtractDir.DIRECTORY_SEPARATOR.$sqlFilenameInZip;
                } else {
                    $zip->close();
                    $this->cleanupRetrievedFile($localPath);

                    return redirect()
                        ->route('admin.backup-restore')
                        ->with('error', 'Restore failed: No .sql file found in the ZIP archive.');
                }
            } else {
                $this->cleanupRetrievedFile($localPath);

                return redirect()
                    ->route('admin.backup-restore')
                    ->with('error', 'Restore failed: Unable to open ZIP file.');
            }
        }

        try {
            $this->createSafetyBackup();
        } catch (\Exception $e) {
            if ($tempExtractDir) {
                $this->cleanTempDir($tempExtractDir);
            }
            $this->cleanupRetrievedFile($localPath);

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Restore aborted: Failed to create safety backup. Error: '.$e->getMessage());
        }

        if (! DatabaseDumpService::restore($restoreSqlPath)) {
            if ($tempExtractDir) {
                $this->cleanTempDir($tempExtractDir);
            }
            $this->cleanupRetrievedFile($localPath);

            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Restore failed: Unable to import database tables.');
        }

        if ($tempExtractDir) {
            $this->cleanTempDir($tempExtractDir);
        }
        $this->cleanupRetrievedFile($localPath);

        ActivityLog::log(
            'DATABASE_RESTORE',
            'Database restored from server backup file: '.$filename
        );

        return redirect()
            ->route('admin.backup-restore')
            ->with('success', 'Database restored successfully from server backup "'.$filename.'".');
    }

    /**
     * Download a specific backup file from the configured backup disk.
     */
    public function downloadLocal(string $filename): BinaryFileResponse|StreamedResponse
    {
        return $this->backupStorage->download($filename);
    }

    /**
     * Delete a specific backup file from the configured backup disk.
     */
    public function deleteLocal(string $filename): RedirectResponse
    {
        if (! $this->backupStorage->isBackupFile($filename)) {
            abort(400, 'Invalid backup filename.');
        }

        if (! $this->backupStorage->exists($filename)) {
            return redirect()
                ->route('admin.backup-restore')
                ->with('error', 'Backup file not found on server.');
        }

        $this->backupStorage->delete($filename);

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
     * Creates a safety backup of the current database state before restoring.
     * Throws an Exception on failure.
     */
    private function createSafetyBackup(): void
    {
        $now = now();
        $sqlFilename = 'safety_temp_'.$now->format('Y-m-d_H-i-s').'.sql';
        $zipFilename = 'safety-backup-'.$now->format('Y-m-d_H-i-s').'.zip';

        $tempDir = storage_path('app/temp_safety_'.uniqid());
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempSqlFilePath = $tempDir.DIRECTORY_SEPARATOR.$sqlFilename;
        $tempZipFilePath = $tempDir.DIRECTORY_SEPARATOR.$zipFilename;

        if (! DatabaseDumpService::dump($tempSqlFilePath)) {
            $this->cleanTempDir($tempDir);
            throw new \Exception('Failed to generate database dump for safety backup.');
        }

        $finalPath = $tempSqlFilePath;
        $finalFilename = $sqlFilename;

        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive;
            if ($zip->open($tempZipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFile($tempSqlFilePath, $sqlFilename);
                $zip->close();
                @unlink($tempSqlFilePath);
                $finalPath = $tempZipFilePath;
                $finalFilename = $zipFilename;
            }
        }

        $stored = $this->backupStorage->store($finalPath, $finalFilename);
        $this->cleanTempDir($tempDir);

        if (! $stored) {
            throw new \Exception('Failed to persist safety backup to storage.');
        }

        ActivityLog::log(
            'DATABASE_BACKUP',
            'Safety backup created automatically before restore: '.$finalFilename
        );
    }

    private function cleanTempDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = @scandir($dir) ?: [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                @unlink($dir.DIRECTORY_SEPARATOR.$file);
            }
        }
        @rmdir($dir);
    }

    private function cleanupRetrievedFile(string $localPath): void
    {
        if (str_contains($localPath, 'temp_restore')) {
            @unlink($localPath);
        }
    }
}
