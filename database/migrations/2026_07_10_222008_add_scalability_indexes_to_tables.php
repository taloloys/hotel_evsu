<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->index(['last_name', 'first_name'], 'idx_guests_search_name');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['arrival_date', 'departure_date'], 'idx_bookings_arrival_departure');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['transaction_date', 'timestamp'], 'idx_transactions_reporting_date');
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->index('status', 'idx_folios_status');
        });

        Schema::table('activitylogs', function (Blueprint $table) {
            $table->index('timestamp', 'idx_activitylogs_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex('idx_guests_search_name');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_arrival_departure');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_reporting_date');
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->dropIndex('idx_folios_status');
        });

        Schema::table('activitylogs', function (Blueprint $table) {
            $table->dropIndex('idx_activitylogs_timestamp');
        });
    }
};
