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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('module', 50)->default('System')->after('permission_key');
            $table->boolean('is_active')->default(true)->after('description');
        });

        // Categorize existing system permissions
        DB::table('permissions')->where('permission_key', 'manage-users')->update(['module' => 'System']);
        DB::table('permissions')->whereIn('permission_key', ['manage-reservations', 'process-checkout'])->update(['module' => 'Front Desk']);
        DB::table('permissions')->where('permission_key', 'view-folio')->update(['module' => 'Accounting']);
        DB::table('permissions')->where('permission_key', 'manage-inventory')->update(['module' => 'Inventory']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['module', 'is_active']);
        });
    }
};
