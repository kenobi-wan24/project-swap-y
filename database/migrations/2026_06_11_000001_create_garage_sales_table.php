<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garage_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['active', 'ended', 'draft'])->default('active');
            $table->timestamps();
        });

        Schema::create('garage_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_sale_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('category');
            $table->enum('condition', ['new', 'like_new', 'good', 'fair']);
            $table->integer('value')->default(0);
            $table->string('image_path')->nullable();
            $table->string('wants')->nullable();
            $table->text('swap_terms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garage_sale_items');
        Schema::dropIfExists('garage_sales');
    }
};
