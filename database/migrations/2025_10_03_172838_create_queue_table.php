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
        Schema::create('queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->integer('party_size');
            $table->string('token_number')->unique();
            $table->enum('status', ['waiting', 'called', 'seated', 'cancelled'])->default('waiting');
            $table->integer('estimated_wait_time')->nullable(); // in minutes
            $table->timestamp('joined_at');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('seated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue');
    }
};
