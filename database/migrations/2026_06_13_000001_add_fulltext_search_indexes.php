<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FULLTEXT indexes powering universal search. MySQL MATCH(...) AGAINST(...)
 * requires the searched column set to exactly match one of these indexes,
 * so the column lists here mirror the MATCH() calls in SearchController.
 * Only CHAR/VARCHAR/TEXT columns are eligible (enums/JSON are excluded).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->fullText(['title', 'description', 'category', 'location', 'looking_for'], 'items_fulltext');
        });

        Schema::table('homes', function (Blueprint $table) {
            $table->fullText(['title', 'description', 'location'], 'homes_fulltext');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->fullText(['title', 'description', 'category', 'location'], 'services_fulltext');
        });

        Schema::table('garage_sales', function (Blueprint $table) {
            $table->fullText(['title', 'description', 'location'], 'garage_sales_fulltext');
        });

        Schema::table('garage_sale_items', function (Blueprint $table) {
            $table->fullText(['title', 'category'], 'garage_sale_items_fulltext');
        });
    }

    public function down(): void
    {
        Schema::table('items', fn (Blueprint $t) => $t->dropFullText('items_fulltext'));
        Schema::table('homes', fn (Blueprint $t) => $t->dropFullText('homes_fulltext'));
        Schema::table('services', fn (Blueprint $t) => $t->dropFullText('services_fulltext'));
        Schema::table('garage_sales', fn (Blueprint $t) => $t->dropFullText('garage_sales_fulltext'));
        Schema::table('garage_sale_items', fn (Blueprint $t) => $t->dropFullText('garage_sale_items_fulltext'));
    }
};
