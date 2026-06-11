<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('category');
            $table->text('description')->nullable();
            $table->unsignedInteger('rate')->nullable();
            $table->string('rate_type')->nullable();   // e.g., 'Per hour', 'Per project'
            $table->enum('delivery', ['Remote', 'In-person', 'Both'])->default('Both');
            $table->text('swap_for')->nullable();
            $table->json('tags')->nullable();          // highlights / perks
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['active', 'ended', 'draft'])->default('active');
            $table->timestamps();
        });

        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
        Schema::dropIfExists('services');
    }
};
