<?php

namespace App\Services;

use App\Models\SystemSetting;
use Throwable;

class BackupSettingsService
{
    protected static string $fileName = 'backup_settings.json';

    /**
     * Default backup settings.
     */
    public static function defaults(): array
    {
        return [
            'enabled' => false,
            'time' => '02:00',
            'retention_days' => 30,
            'folder' => storage_path('backups'),
        ];
    }

    /**
     * Get the current backup settings, merging with defaults.
     */
    public static function get(): array
    {
        $default = static::defaults();

        // 1. Try reading from database (SystemSetting)
        try {
            $json = SystemSetting::get('backup_settings');
            if ($json) {
                $data = json_decode($json, true);
                if (is_array($data)) {
                    return array_merge($default, $data);
                }
            }
        } catch (Throwable $e) {
            // Database may be offline, connecting, or tables not yet migrated
        }

        // 2. Backwards-compatibility fallback: check storage/app/backup_settings.json
        $filePath = storage_path('app/'.self::$fileName);
        if (file_exists($filePath)) {
            try {
                $data = json_decode(file_get_contents($filePath), true);
                if (is_array($data)) {
                    return array_merge($default, $data);
                }
            } catch (Throwable $e) {
                // Silently fallback to defaults
            }
        }

        return $default;
    }

    /**
     * Save the backup settings to database with file fallback.
     */
    public static function set(array $settings): void
    {
        $current = static::get();
        $merged = array_merge($current, $settings);

        // Normalize data types
        if (isset($merged['enabled'])) {
            $merged['enabled'] = (bool) $merged['enabled'];
        }
        if (isset($merged['retention_days'])) {
            $merged['retention_days'] = max(1, (int) $merged['retention_days']);
        }

        try {
            SystemSetting::set('backup_settings', json_encode($merged));
        } catch (Throwable $e) {
            // Database fallback to local file
            $dir = storage_path('app');
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            @file_put_contents(
                storage_path('app/'.self::$fileName),
                json_encode($merged, JSON_PRETTY_PRINT)
            );
        }
    }

    /**
     * Alias for set() for clean API compliance.
     */
    public static function update(array $settings): void
    {
        static::set($settings);
    }
}
