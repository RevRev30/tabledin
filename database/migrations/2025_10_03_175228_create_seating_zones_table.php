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
        Schema::create('seating_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('seating_layout_id')->constrained()->onDelete('cascade');
            $table->string('zone_name'); // Window Side, Bar Area, Private Dining, etc.
            $table->text('description')->nullable();
            $table->json('zone_coordinates'); // X,Y coordinates for zone boundaries
            $table->string('zone_color')->default('#3498db'); // Color for visual representation
            $table->integer('max_capacity')->nullable(); // Maximum capacity for this zone
            $table->json('amenities')->nullable(); // Zone-specific amenities
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_zones');
    }
};
