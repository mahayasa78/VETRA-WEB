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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('target_id');
            $table->enum('target_type', ['doctor', 'clinic']);
            $table->tinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
            $table->index(['target_id', 'target_type']);
            $table->unique(['user_id', 'target_id', 'target_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
