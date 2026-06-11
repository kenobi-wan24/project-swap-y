<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['items', 'homes', 'garage_sales', 'services'];

    public function up(): void
    {
        // Convert the rigid status enums to a flexible string so listings can be
        // active / paused / ended / etc. (validated at the application layer).
        foreach ($this->tables as $t) {
            if (Schema::hasColumn($t, 'status')) {
                DB::statement("ALTER TABLE `$t` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'active'");
            }
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `items` MODIFY `status` ENUM('active','swapped','removed') NOT NULL DEFAULT 'active'");
        foreach (['homes', 'garage_sales', 'services'] as $t) {
            DB::statement("ALTER TABLE `$t` MODIFY `status` ENUM('active','ended','draft') NOT NULL DEFAULT 'active'");
        }
    }
};
