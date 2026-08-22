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
        Schema::dropIfExists('usermodulepreferences');

        // Remove the permission if it exists
        DB::table('permissions')->where('permission_key', 'manage-sidebar-settings')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('usermodulepreferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('module_key');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'module_key']);
        });

        DB::table('permissions')->insert([
            'permission_key' => 'manage-sidebar-settings',
            'description' => 'Configure sidebar module visibility settings',
            'module' => 'System',
            'is_active' => true,
        ]);
    }
};
