<?php

namespace App\Console\Commands;

use App\Services\DataArchivingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArchiveOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-old-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive transactional data older than 1 year';

    /**
     * Execute the console command.
     */
    public function handle(DataArchivingService $archivingService)
    {
        $this->info('Starting data archiving process...');

        $thresholdDate = Carbon::now()->subDays(365)->startOfDay();

        $tablesToArchive = [
            [
                'source' => 'transactions',
                'archive' => 'archived_transactions',
                'dateColumn' => 'transaction_date',
                'primaryKey' => 'transaction_id',
            ],
            [
                'source' => 'expenses',
                'archive' => 'archived_expenses',
                'dateColumn' => 'expense_date',
                'primaryKey' => 'expense_id',
            ],
            [
                'source' => 'activitylogs',
                'archive' => 'archived_activitylogs',
                'dateColumn' => 'timestamp',
                'primaryKey' => 'log_id',
            ],
            [
                'source' => 'pos_orders',
                'archive' => 'archived_pos_orders',
                'dateColumn' => 'closed_at',
                'primaryKey' => 'order_id',
            ],
            [
                'source' => 'pos_order_items',
                'archive' => 'archived_pos_order_items',
                // pos_order_items doesn't have a date column natively. We should join with pos_orders.
                // Wait! To archive pos_order_items, we need the date from pos_orders.
                // Actually, DataArchivingService does simple where($dateColumn, '<', $threshold).
                // Let me implement a join workaround or just skip it here and do it custom.
                // Or better, change pos_order_items to rely on order_id which is archived in pos_orders.
            ],
        ];

        foreach ($tablesToArchive as $config) {
            if ($config['source'] === 'pos_order_items') {
                // Special handling for pos_order_items, join with pos_orders
                $this->archivePosOrderItems($archivingService, $thresholdDate);

                continue;
            }

            $this->info("Archiving {$config['source']}...");
            $archivedCount = $archivingService->executeArchiving(
                $config['source'],
                $config['archive'],
                $config['dateColumn'],
                $config['primaryKey'],
                $thresholdDate
            );
            $this->info("Archived {$archivedCount} records from {$config['source']}.");
        }

        // Cache the last run time for 7 days
        Cache::put('last_archive_run', now(), now()->addDays(7));

        $this->info('Data archiving process completed.');
    }

    private function archivePosOrderItems(DataArchivingService $archivingService, Carbon $thresholdDate)
    {
        $this->info('Archiving pos_order_items...');

        $totalArchived = 0;
        $chunkSize = 500;

        while (true) {
            $records = DB::table('pos_order_items')
                ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
                ->where('pos_orders.closed_at', '<', $thresholdDate)
                ->select('pos_order_items.*')
                ->limit($chunkSize)
                ->get();

            if ($records->isEmpty()) {
                break;
            }

            $recordIds = $records->pluck('order_item_id')->toArray();

            $insertData = $records->map(function ($record) {
                $array = (array) $record;
                $array['archived_at'] = now();

                return $array;
            })->toArray();

            DB::transaction(function () use ($insertData, $recordIds) {
                DB::table('archived_pos_order_items')->insert($insertData);
                DB::table('pos_order_items')->whereIn('order_item_id', $recordIds)->delete();
            });

            $totalArchived += count($recordIds);
        }

        $this->info("Archived {$totalArchived} records from pos_order_items.");
    }
}
