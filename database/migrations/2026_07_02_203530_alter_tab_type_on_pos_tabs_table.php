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
        Schema::table('pos_tabs', function (Blueprint $table) {
            $table->string('tab_type', 50)->default('walk_in')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting back to enum can be tricky and may result in data loss if 'account' exists,
        // so we leave it as string in the down method.
    }
};
