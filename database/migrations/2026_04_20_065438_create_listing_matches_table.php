<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listing_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('matched_listing_id')->constrained('listings')->cascadeOnDelete();
            $table->decimal('score', 5, 4);
            $table->json('match_reasons')->nullable();
            $table->timestamps();

            $table->unique(['listing_id', 'matched_listing_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_matches');
    }
};