<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupStorageService
{
    /**
     * Get the configured backup disk name.
     */
    public function getDiskName(): string
    {
        return config('filesystems.backup_disk', 'local');
    }

    /**
     * Store a local backup artifact file to the configured backup disk.
     */
    public function store(string $localSourcePath, string $filename): bool
    {
        if (! file_exists($localSourcePath) || filesize($localSourcePath) === 0) {
            Log::error("BackupStorageService::store failed: source file does not exist or is empty [{$localSourcePath}].");

            return false;
        }

        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);
        $remotePath = 'backups/'.$filename;

        $stream = @fopen($localSourcePath, 'r');
        if (! $stream) {
            Log::error("BackupStorageService::store failed: unable to open read stream for [{$localSourcePath}].");

            return false;
        }

        $stored = $disk->put($remotePath, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        if (! $stored) {
            Log::error("BackupStorageService::store failed: disk [{$diskName}] put returned false for [{$remotePath}].");

            return false;
        }

        // Verify storage succeeded
        if (! $disk->exists($remotePath) || $disk->size($remotePath) <= 0) {
            Log::error("BackupStorageService::store failed: verification failed on disk [{$diskName}] for [{$remotePath}].");
            $disk->delete($remotePath);

            return false;
        }

        // For local development backwards compatibility: mirror to configured folder if local
        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $folder = $settings['folder'] ?? storage_path('backups');
            if ($folder && is_dir($folder)) {
                $localDestination = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
                if ($localSourcePath !== $localDestination) {
                    @copy($localSourcePath, $localDestination);
                }
            }
        }

        return true;
    }

    /**
     * List all available backups from the configured backup disk.
     *
     * @return list<array{filename: string, size: string, raw_size: int, created_at: string, timestamp: int, disk: string}>
     */
    public function list(): array
    {
        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);
        $backups = [];
        $seenFilenames = [];

        try {
            $files = $disk->files('backups');
            foreach ($files as $file) {
                $filename = basename($file);
                if (! $this->isBackupFile($filename)) {
                    continue;
                }

                $size = (int) $disk->size($file);
                $timestamp = (int) $disk->lastModified($file);

                $backups[] = [
                    'filename' => $filename,
                    'size' => $this->formatBytes($size),
                    'raw_size' => $size,
                    'created_at' => $timestamp ? date('Y-m-d H:i:s', $timestamp) : 'Unknown',
                    'timestamp' => $timestamp,
                    'disk' => $diskName,
                ];
                $seenFilenames[$filename] = true;
            }
        } catch (\Throwable $e) {
            Log::warning('BackupStorageService::list failed on disk ['.$diskName.']: '.$e->getMessage());
        }

        // When disk is local, also inspect legacy local directory
        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $candidateFolders = array_unique(array_filter([
                $settings['folder'] ?? null,
                storage_path('backups'),
            ]));

            foreach ($candidateFolders as $folder) {
                if (is_dir($folder)) {
                    $entries = @scandir($folder) ?: [];
                    foreach ($entries as $entry) {
                        if ($entry === '.' || $entry === '..' || isset($seenFilenames[$entry])) {
                            continue;
                        }

                        if (! $this->isBackupFile($entry)) {
                            continue;
                        }

                        $filePath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$entry;
                        $size = file_exists($filePath) ? filesize($filePath) : 0;
                        $mtime = file_exists($filePath) ? filemtime($filePath) : 0;

                        $backups[] = [
                            'filename' => $entry,
                            'size' => $this->formatBytes($size),
                            'raw_size' => $size,
                            'created_at' => $mtime ? date('Y-m-d H:i:s', $mtime) : 'Unknown',
                            'timestamp' => $mtime,
                            'disk' => 'local_legacy',
                        ];
                        $seenFilenames[$entry] = true;
                    }
                }
            }
        }

        usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    /**
     * Check if a backup exists.
     */
    public function exists(string $filename): bool
    {
        if (! $this->isBackupFile($filename)) {
            return false;
        }

        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);

        if ($disk->exists('backups/'.$filename)) {
            return true;
        }

        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $candidateFolders = array_unique(array_filter([
                $settings['folder'] ?? null,
                storage_path('backups'),
            ]));

            foreach ($candidateFolders as $folder) {
                $localPath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
                if (file_exists($localPath)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Download a backup file securely without exposing S3 credentials.
     */
    public function download(string $filename): StreamedResponse|BinaryFileResponse
    {
        if (! $this->isBackupFile($filename)) {
            abort(400, 'Invalid backup filename.');
        }

        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);
        $remotePath = 'backups/'.$filename;

        if ($disk->exists($remotePath)) {
            return $disk->download($remotePath, $filename, [
                'Content-Type' => 'application/octet-stream',
            ]);
        }

        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $candidateFolders = array_unique(array_filter([
                $settings['folder'] ?? null,
                storage_path('backups'),
            ]));

            foreach ($candidateFolders as $folder) {
                $localPath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
                if (file_exists($localPath)) {
                    return response()->download($localPath, $filename, [
                        'Content-Type' => 'application/octet-stream',
                    ]);
                }
            }
        }

        abort(404, 'Backup file not found.');
    }

    /**
     * Alias for download() to provide standard response naming.
     */
    public function downloadResponse(string $filename): StreamedResponse|BinaryFileResponse
    {
        return $this->download($filename);
    }

    /**
     * Delete a backup file from storage.
     */
    public function delete(string $filename): bool
    {
        if (! $this->isBackupFile($filename)) {
            return false;
        }

        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);
        $deleted = false;

        if ($disk->exists('backups/'.$filename)) {
            $deleted = $disk->delete('backups/'.$filename);
        }

        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $candidateFolders = array_unique(array_filter([
                $settings['folder'] ?? null,
                storage_path('backups'),
            ]));

            foreach ($candidateFolders as $folder) {
                $localPath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
                if (file_exists($localPath)) {
                    $deleted = @unlink($localPath) || $deleted;
                }
            }
        }

        return $deleted;
    }

    /**
     * Retrieve a local file path copy of the backup for database restoration.
     * The returned local file should be unlinked by the caller after restore.
     */
    public function getLocalCopy(string $filename): ?string
    {
        if (! $this->isBackupFile($filename)) {
            return null;
        }

        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);
        $remotePath = 'backups/'.$filename;

        // If stored on disk (S3 or local storage), stream to a temporary local file
        if ($disk->exists($remotePath)) {
            $tempDir = storage_path('app/temp_restore');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $tempFilePath = $tempDir.DIRECTORY_SEPARATOR.'restore_'.uniqid().'.'.$ext;

            $readStream = $disk->readStream($remotePath);
            if (! $readStream) {
                return null;
            }

            $writeHandle = @fopen($tempFilePath, 'w');
            if (! $writeHandle) {
                if (is_resource($readStream)) {
                    fclose($readStream);
                }

                return null;
            }

            stream_copy_to_stream($readStream, $writeHandle);
            if (is_resource($readStream)) {
                fclose($readStream);
            }
            if (is_resource($writeHandle)) {
                fclose($writeHandle);
            }

            return file_exists($tempFilePath) ? $tempFilePath : null;
        }

        // If local legacy file exists
        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $candidateFolders = array_unique(array_filter([
                $settings['folder'] ?? null,
                storage_path('backups'),
            ]));

            foreach ($candidateFolders as $folder) {
                $localPath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
                if (file_exists($localPath)) {
                    return $localPath;
                }
            }
        }

        return null;
    }

    /**
     * Clean up backups older than the specified days.
     */
    public function cleanup(int $days): int
    {
        $diskName = $this->getDiskName();
        $disk = Storage::disk($diskName);
        $deletedCount = 0;

        try {
            $files = $disk->files('backups');
            foreach ($files as $file) {
                $filename = basename($file);
                if (! $this->isBackupFile($filename)) {
                    continue;
                }

                $timestamp = $disk->lastModified($file);
                if ($timestamp && Carbon::createFromTimestamp($timestamp)->addDays($days)->isPast()) {
                    if ($disk->delete($file)) {
                        $deletedCount++;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('BackupStorageService::cleanup error on disk ['.$diskName.']: '.$e->getMessage());
        }

        if ($diskName === 'local') {
            $settings = BackupSettingsService::get();
            $folder = $settings['folder'] ?? storage_path('backups');
            if (is_dir($folder)) {
                $entries = @scandir($folder) ?: [];
                foreach ($entries as $entry) {
                    if ($entry === '.' || $entry === '..' || ! $this->isBackupFile($entry)) {
                        continue;
                    }

                    $filePath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$entry;
                    $mtime = file_exists($filePath) ? filemtime($filePath) : 0;
                    if ($mtime && Carbon::createFromTimestamp($mtime)->addDays($days)->isPast()) {
                        if (@unlink($filePath)) {
                            $deletedCount++;
                        }
                    }
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Check if filename matches backup file patterns (.sql or .zip) and cannot escape directory.
     */
    public function isBackupFile(string $filename): bool
    {
        if ($filename !== basename($filename) || str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            return false;
        }

        return (bool) preg_match('/^[a-zA-Z0-9_\-\s\.]+\.(sql|zip)$/i', $filename);
    }

    /**
     * Format bytes to human readable format.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min((int) $pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
