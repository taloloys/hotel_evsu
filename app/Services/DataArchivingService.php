<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataArchivingService
{
    /**
     * Executes the archiving process for a given table.
     *
     * @param  string  $sourceTable  The name of the primary active table.
     * @param  string  $archiveTable  The name of the target archive table.
     * @param  string  $dateColumn  The date column used to filter old records.
     * @param  string  $primaryKey  The primary key column of the table.
     * @param  Carbon|string  $thresholdDate  The cutoff date. Records older than this are archived.
     * @param  int  $chunkSize  The number of records to process at once.
     * @return int The total number of records archived.
     */
    public function executeArchiving(
        string $sourceTable,
        string $archiveTable,
        string $dateColumn,
        string $primaryKey,
        $thresholdDate,
        int $chunkSize = 500
    ): int {
        $totalArchived = 0;

        while (true) {
            // Get a chunk of records that match the criteria
            $records = DB::table($sourceTable)
                ->where($dateColumn, '<', $thresholdDate)
                ->limit($chunkSize)
                ->get();

            if ($records->isEmpty()) {
                break; // No more records to archive
            }

            $recordIds = $records->pluck($primaryKey)->toArray();

            // Prepare records for insertion, adding archived_at timestamp
            $insertData = $records->map(function ($record) {
                $array = (array) $record;
                $array['archived_at'] = now();

                return $array;
            })->toArray();

            // Perform archiving in an ACID transaction
            DB::transaction(function () use ($sourceTable, $archiveTable, $primaryKey, $insertData, $recordIds) {
                // 1. Insert into archive table
                DB::table($archiveTable)->insert($insertData);

                // 2. Delete from source table
                DB::table($sourceTable)->whereIn($primaryKey, $recordIds)->delete();
            });

            $totalArchived += count($recordIds);

            // Log progression safely
            Log::info('Archived chunk of '.count($recordIds)." records from {$sourceTable} to {$archiveTable}");
        }

        return $totalArchived;
    }
}
