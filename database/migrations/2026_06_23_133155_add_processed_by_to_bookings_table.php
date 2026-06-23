<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->unsignedInteger('checked_in_by')->nullable()->after('status')
                ->comment('User ID of the employee who performed the check-in');

            $table->foreign('checked_in_by')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropForeign(['checked_in_by']);
            $table->dropColumn('checked_in_by');
        });
    }
};
