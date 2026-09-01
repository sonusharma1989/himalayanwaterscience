<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('gst_place_of_supply')->nullable()->after('gstin');
            $table->string('gst_tax_type', 20)->nullable()->after('gst_place_of_supply');
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['gst_place_of_supply', 'gst_tax_type']));
    }
};
