<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->string('event_name')->nullable()->after('booker_contact');
            $table->text('event_details')->nullable()->after('event_name');
            $table->enum('billing_type', ['hourly', 'daily'])->default('hourly')->after('event_details');
            $table->date('end_date')->nullable()->after('reservation_date');
            $table->unsignedSmallInteger('total_days')->default(1)->after('end_date');
            $table->decimal('agreed_rate', 10, 2)->nullable()->after('duration_hours');
            $table->decimal('final_amount', 10, 2)->nullable()->after('estimated_amount');
            $table->timestamp('actual_start_time')->nullable()->after('processed_at');
            $table->timestamp('actual_end_time')->nullable()->after('actual_start_time');
            $table->string('status', 30)->default('pending')->change();
        });

        // Backfill end_date = reservation_date for existing rows
        DB::table('facility_reservations')->whereNull('end_date')->update([
            'end_date' => DB::raw('reservation_date'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'event_name',
                'event_details',
                'billing_type',
                'end_date',
                'total_days',
                'agreed_rate',
                'final_amount',
                'actual_start_time',
                'actual_end_time',
            ]);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};
