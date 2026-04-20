<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 8, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->unsignedInteger('max_listings')->nullable();
            $table->boolean('can_feature')->default(false);
            $table->boolean('can_three_way_swap')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};