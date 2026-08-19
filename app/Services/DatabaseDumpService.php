<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class DatabaseDumpService
{
    /**
     * Perform database dump to target SQL file path.
     * Tries CLI mysqldump first, falling back to pure PHP PDO export if CLI is unavailable.
     */
    public static function dump(string $targetSqlPath): bool
    {
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath === ':memory:') {
                return file_put_contents($targetSqlPath, '-- sqlite memory backup') !== false;
            }

            return file_exists($dbPath) && @copy($dbPath, $targetSqlPath);
        }

        // Try CLI mysqldump first
        if (function_exists('exec')) {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $mysqldump = static::resolveBinary('mysqldump');
            $passwordArg = $password ? '-p'.escapeshellarg($password) : '';

            $command = sprintf(
                '%s --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --result-file=%s %s 2>&1',
                escapeshellarg($mysqldump),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passwordArg,
                escapeshellarg($targetSqlPath),
                escapeshellarg($database)
            );

            @exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($targetSqlPath) && filesize($targetSqlPath) > 0) {
                return true;
            }
        }

        // Fallback to Pure PHP PDO dump if CLI failed or unavailable
        return static::dumpWithPhpPdo($targetSqlPath);
    }

    /**
     * Restore database from SQL file.
     * Tries CLI mysql first, falling back to pure PHP PDO execution.
     */
    public static function restore(string $sqlFilePath): bool
    {
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath !== ':memory:') {
                return @copy($sqlFilePath, $dbPath);
            }

            return true;
        }

        if (function_exists('exec')) {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $mysql = static::resolveBinary('mysql');
            $passwordArg = $password ? '-p'.escapeshellarg($password) : '';

            $command = sprintf(
                '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
                escapeshellarg($mysql),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passwordArg,
                escapeshellarg($database),
                escapeshellarg($sqlFilePath)
            );

            @exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                return true;
            }
        }

        // Fallback to Pure PHP PDO restore
        return static::restoreWithPhpPdo($sqlFilePath);
    }

    /**
     * Pure PHP PDO database dumper.
     */
    protected static function dumpWithPhpPdo(string $targetSqlPath): bool
    {
        try {
            $pdo = DB::connection()->getPdo();
            $tables = [];
            $stmt = $pdo->query('SHOW TABLES');
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $handle = fopen($targetSqlPath, 'w');
            if (! $handle) {
                return false;
            }

            fwrite($handle, "-- Hotel EVSU Pure PHP Database Dump\n");
            fwrite($handle, '-- Generated: '.date('Y-m-d H:i:s')."\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $table) {
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createStmt->fetch(PDO::FETCH_NUM);
                fwrite($handle, $createRow[1].";\n\n");

                $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
                while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
                    $keys = array_map(fn ($k) => "`{$k}`", array_keys($row));
                    $values = array_map(function ($val) use ($pdo) {
                        if ($val === null) {
                            return 'NULL';
                        }

                        return $pdo->quote($val);
                    }, array_values($row));

                    $line = 'INSERT INTO `'.$table.'` ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).");\n";
                    fwrite($handle, $line);
                }
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            return true;
        } catch (\Throwable $e) {
            Log::error('PHP PDO DB dump failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Pure PHP PDO database restorer.
     */
    protected static function restoreWithPhpPdo(string $sqlFilePath): bool
    {
        try {
            $sql = file_get_contents($sqlFilePath);
            if (! $sql) {
                return false;
            }

            DB::connection()->unprepared($sql);

            return true;
        } catch (\Throwable $e) {
            Log::error('PHP PDO DB restore failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Resolve path to binary executable.
     */
    protected static function resolveBinary(string $binary): string
    {
        $candidates = [
            "C:\\xampp\\mysql\\bin\\{$binary}.exe",
            ...glob("C:\\wamp64\\bin\\mysql\\*\\bin\\{$binary}.exe") ?: [],
            ...glob("C:\\laragon\\bin\\mysql\\*\\bin\\{$binary}.exe") ?: [],
            "C:\\MAMP\\bin\\mysql\\bin\\{$binary}.exe",
            "/usr/bin/{$binary}",
            "/usr/local/bin/{$binary}",
            "/opt/homebrew/bin/{$binary}",
            "/root/.nix-profile/bin/{$binary}",
            "/nix/var/nix/profiles/default/bin/{$binary}",
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        if (function_exists('exec') && DIRECTORY_SEPARATOR === '/') {
            $whichPath = trim((string) @shell_exec("which {$binary} 2>/dev/null"));
            if ($whichPath && file_exists($whichPath)) {
                return $whichPath;
            }
        }

        return $binary;
    }
}
