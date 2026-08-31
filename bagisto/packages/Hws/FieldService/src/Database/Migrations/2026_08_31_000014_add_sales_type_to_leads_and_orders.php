<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->enum('sales_type', ['trading', 'projects', 'services'])->default('trading')->after('source');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('sales_type', ['trading', 'projects', 'services'])->default('trading')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('hws_site_surveys', fn (Blueprint $table) => $table->dropColumn('sales_type'));
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('sales_type'));
    }
};
