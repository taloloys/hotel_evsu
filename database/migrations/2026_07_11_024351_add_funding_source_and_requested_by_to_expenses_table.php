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
        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('description', 'purpose');
            // Depending on existing data, this change might need to be handled carefully, but we assume it's safe here.
            $table->enum('department', ['Front Office', 'Housekeeping', 'Maintenance', 'Purchasing', 'Food & Beverage'])->change();
            $table->enum('funding_source', ['FRONT DESK', 'CAFETERIA'])->default('FRONT DESK');
            $table->string('requested_by', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('requested_by');
            $table->dropColumn('funding_source');
            $table->string('department', 100)->change();
            $table->renameColumn('purpose', 'description');
        });
    }
};
