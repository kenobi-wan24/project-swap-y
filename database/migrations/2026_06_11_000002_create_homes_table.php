<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['Swap', 'Rent', 'Sell', 'Co-living'])->default('Swap');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('beds')->nullable();              // can be "Studio"
            $table->unsignedTinyInteger('baths')->nullable();
            $table->unsignedInteger('sqm')->nullable();
            $table->unsignedBigInteger('value')->nullable(); // price / monthly rent / est. value
            $table->text('swap_terms')->nullable();
            $table->json('tags')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['active', 'ended', 'draft'])->default('active');
            $table->timestamps();
        });

        Schema::create('home_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_images');
        Schema::dropIfExists('homes');
    }
};
