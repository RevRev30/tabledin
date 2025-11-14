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
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('seating_layout_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('seating_zone_id')->nullable()->constrained()->onDelete('set null');
            $table->json('table_coordinates')->nullable(); // X,Y position on the layout
            $table->integer('table_rotation')->default(0); // Rotation angle in degrees
            $table->string('table_shape')->default('rectangle'); // rectangle, circle, oval
            $table->json('table_dimensions')->nullable(); // Width, height for custom shapes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['seating_layout_id']);
            $table->dropForeign(['seating_zone_id']);
            $table->dropColumn(['seating_layout_id', 'seating_zone_id', 'table_coordinates', 'table_rotation', 'table_shape', 'table_dimensions']);
        });
    }
};
