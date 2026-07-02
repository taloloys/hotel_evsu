<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('chargecodes')->insertOrIgnore([
            'charge_code' => 404,
            'description' => 'ACCOUNT CHARGE',
            'category' => 'PAYMENT',
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('chargecodes')->where('charge_code', 404)->delete();
    }
};
