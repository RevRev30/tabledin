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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('report_name');
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'custom']);
            $table->date('date_from');
            $table->date('date_to');
            $table->json('data'); // Report data in JSON format
            $table->string('file_path')->nullable(); // Path to generated PDF/Excel file
            $table->enum('format', ['pdf', 'excel', 'csv'])->default('pdf');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
