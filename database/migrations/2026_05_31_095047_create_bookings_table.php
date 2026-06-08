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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('clinic_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pet_id')->nullable()->constrained('pets')->onDelete('set null');
            $table->text('complaint')->nullable();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->datetime('scheduled_at');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'done'])->default('pending');
            $table->text('doctor_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
