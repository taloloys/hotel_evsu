<?php

namespace App\Services;

class BackupSettingsService
{
    protected static string $fileName = 'backup_settings.json';

    /**
     * Get the current backup settings, merging with defaults.
     */
    public static function get(): array
    {
        $default = [
            'enabled' => false,
            'time' => '02:00',
            'folder' => storage_path('backups'),
        ];

        $filePath = storage_path('app/'.self::$fileName);

        if (! file_exists($filePath)) {
            return $default;
        }

        try {
            $data = json_decode(file_get_contents($filePath), true);

            return array_merge($default, is_array($data) ? $data : []);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Save the backup settings.
     */
    public static function set(array $settings): void
    {
        $dir = storage_path('app');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            storage_path('app/'.self::$fileName),
            json_encode($settings, JSON_PRETTY_PRINT)
        );
    }
}
