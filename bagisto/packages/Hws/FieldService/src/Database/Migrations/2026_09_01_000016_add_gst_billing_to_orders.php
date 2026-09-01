<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_gst_invoice')->default(false)->after('sales_type');
            $table->string('billing_company_name')->nullable()->after('is_gst_invoice');
            $table->string('gstin', 15)->nullable()->after('billing_company_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['is_gst_invoice', 'billing_company_name', 'gstin']));
    }
};
