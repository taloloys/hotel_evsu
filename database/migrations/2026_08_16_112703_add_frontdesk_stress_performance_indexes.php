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
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['status', 'arrival_date'], 'idx_bookings_status_arrival');
            $table->index(['status', 'departure_date'], 'idx_bookings_status_departure');
            $table->index('arrival_date', 'idx_bookings_arrival_date');
            $table->index('departure_date', 'idx_bookings_departure_date');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->index(['last_name', 'first_name'], 'idx_guests_names');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['shift_id', 'payment_method'], 'idx_tx_shift_pay_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_status_arrival');
            $table->dropIndex('idx_bookings_status_departure');
            $table->dropIndex('idx_bookings_arrival_date');
            $table->dropIndex('idx_bookings_departure_date');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex('idx_guests_names');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_tx_shift_pay_method');
        });
    }
};
