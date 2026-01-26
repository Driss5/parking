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
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->decimal('tariffs', 8, 2);
            $table->integer('capacity');
            $table->integer('available_spots');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('number');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
