<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['homes', 'garage_sales', 'services'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && ! Schema::hasColumn($name, 'is_promoted')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->boolean('is_promoted')->default(false);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasColumn($name, 'is_promoted')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->dropColumn('is_promoted');
                });
            }
        }
    }
};
