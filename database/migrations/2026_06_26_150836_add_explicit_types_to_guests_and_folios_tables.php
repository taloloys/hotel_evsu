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
            $table->string('guest_type', 20)->default('GUEST')->after('contact_number');
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->string('folio_type', 20)->default('GUEST')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('guest_type');
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->enum('folio_type', ['GUEST', 'HOUSE', 'ALL'])->default('GUEST')->change();
        });
    }
};
