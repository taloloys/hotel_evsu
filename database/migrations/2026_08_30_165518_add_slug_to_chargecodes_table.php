<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a stable `slug` column to the chargecodes table so that application
     * code can reference charge codes by slug instead of hardcoded numbers.
     */
    public function up(): void
    {
        Schema::table('chargecodes', function (Blueprint $table) {
            $table->string('slug', 50)->nullable()->unique()->after('charge_code');
        });

        // Seed slugs for all well-known charge codes that already exist.
        $slugMap = [
            100 => 'room_charge',
            101 => 'gov_tax',
            102 => 'service_charge',
            103 => 'extra_pax',
            104 => 'laundry_service',
            105 => 'pressing',
            106 => 'incoming_fax',
            107 => 'outgoing_fax',
            108 => 'function_room',
            109 => 'long_distance_idd',
            110 => 'long_distance_ndd',
            114 => 'transfer_balance',
            115 => 'other_charges',
            200 => 'food_beverage',
            201 => 'complimentary',
            401 => 'credit_card',
            402 => 'visa',
            403 => 'cash',
            404 => 'account_charge',
            405 => 'gcash',
            406 => 'maya',
        ];

        foreach ($slugMap as $code => $slug) {
            DB::table('chargecodes')
                ->where('charge_code', $code)
                ->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chargecodes', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
