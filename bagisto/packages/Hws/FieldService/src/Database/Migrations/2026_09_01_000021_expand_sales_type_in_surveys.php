<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'pool' and 'retail' / 'retail_sales' to sales_type enum or convert to varchar for flexible future sales types
        if (Schema::hasTable('hws_site_surveys')) {
            DB::statement("ALTER TABLE `hws_site_surveys` MODIFY COLUMN `sales_type` VARCHAR(50) NOT NULL DEFAULT 'trading'");
        }

        if (Schema::hasTable('orders')) {
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN `sales_type` VARCHAR(50) NOT NULL DEFAULT 'trading'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hws_site_surveys')) {
            DB::statement("ALTER TABLE `hws_site_surveys` MODIFY COLUMN `sales_type` ENUM('trading', 'projects', 'services') NOT NULL DEFAULT 'trading'");
        }
    }
};
