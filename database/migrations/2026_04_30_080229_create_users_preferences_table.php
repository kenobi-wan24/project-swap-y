<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('categories')->nullable();
            $table->unsignedInteger('value_min')->default(0);
            $table->unsignedInteger('value_max')->default(1000);
            $table->unsignedInteger('max_distance')->default(25);
            $table->enum('notification_frequency', ['frequent', 'balanced', 'minimal'])->default('balanced');
            $table->enum('intent', ['post', 'explore'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};