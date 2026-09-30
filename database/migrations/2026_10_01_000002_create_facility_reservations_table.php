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
        Schema::create('facility_reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->foreignId('facility_id')->constrained('facilities', 'facility_id')->cascadeOnDelete();
            $table->string('booker_name');
            $table->string('booker_email');
            $table->string('booker_contact', 30);
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration_hours', 5, 2)->nullable();
            $table->decimal('estimated_amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('reference_number')->unique();
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedInteger('processed_by')->nullable();
            $table->foreign('processed_by')->references('user_id')->on('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Composite index for conflict-detection queries
            $table->index(['facility_id', 'reservation_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_reservations');
    }
};
