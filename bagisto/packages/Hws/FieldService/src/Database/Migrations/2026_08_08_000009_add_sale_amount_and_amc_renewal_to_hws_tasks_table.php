<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hws_tasks', function (Blueprint $table) {
            // Filled in when a Sales Visit / Installation task closes with a sale.
            // Powers the "Sales this month" dashboard card.
            $table->decimal('sale_amount', 12, 2)->nullable()->after('rating');

            // Only meaningful for type = 'amc_service'. Powers the
            // "AMC renewals due" dashboard card (renewal date within
            // the next 30 days).
            $table->date('amc_renewal_date')->nullable()->after('sale_amount');

            $table->index('amc_renewal_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hws_tasks', function (Blueprint $table) {
            $table->dropIndex(['amc_renewal_date']);
            $table->dropColumn(['sale_amount', 'amc_renewal_date']);
        });
    }
};
