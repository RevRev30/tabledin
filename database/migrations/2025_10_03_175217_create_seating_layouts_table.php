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
        Schema::create('seating_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('layout_name'); // Main Dining, Private Room, Outdoor, etc.
            $table->text('description')->nullable();
            $table->json('layout_data'); // SVG/Canvas coordinates and table positions
            $table->integer('width')->default(800); // Layout width in pixels
            $table->integer('height')->default(600); // Layout height in pixels
            $table->string('background_image')->nullable(); // Floor plan image
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_layouts');
    }
};
