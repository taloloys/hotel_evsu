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
        if (! Schema::hasColumn('guests', 'email')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->string('email', 100)->nullable()->after('contact_number');
            });
        }

        if (! Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email', 100)->nullable()->after('full_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('guests', 'email')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }

        if (Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
