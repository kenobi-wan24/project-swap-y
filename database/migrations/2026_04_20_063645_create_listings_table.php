<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('condition', ['new', 'like_new', 'good', 'fair', 'poor']);
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();        
            $table->boolean('is_for_swap')->default(false);
            $table->boolean('is_for_sale')->default(false);
            $table->boolean('is_service')->default(false);
            $table->boolean('is_garage_sale')->default(false);
            $table->enum('status', ['active', 'reserved', 'swapped', 'sold', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('zip_code', 10)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};