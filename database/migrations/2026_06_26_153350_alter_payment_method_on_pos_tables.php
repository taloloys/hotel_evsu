<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->string('payment_method', 30)->nullable()->change();
        });

        Schema::table('pos_tabs', function (Blueprint $table) {
            $table->string('payment_method', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Leave as string since returning to enum under SQLite/MySQL can have mixed support
    }
};
