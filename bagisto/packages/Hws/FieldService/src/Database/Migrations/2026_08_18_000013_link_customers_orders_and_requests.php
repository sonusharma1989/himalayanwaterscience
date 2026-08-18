<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable()->after('task_id');
            $table->unsignedInteger('order_id')->nullable()->after('customer_id');
            $table->string('request_type', 50)->nullable()->after('source');
            $table->string('reference_no', 50)->nullable()->unique()->after('request_type');
            $table->json('request_details')->nullable()->after('reference_no');

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->unique('order_id', 'hws_leads_order_unique');
        });

        Schema::table('hws_tasks', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable()->after('id');
            $table->unsignedInteger('order_id')->nullable()->after('customer_id');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->string('source', 100)->nullable()->after('type');
            $table->string('reference_no', 50)->nullable()->unique()->after('source');

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hws_tasks', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['order_id']);
            $table->dropColumn(['customer_id', 'order_id', 'customer_email', 'source', 'reference_no']);
        });

        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->dropUnique('hws_leads_order_unique');
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['order_id']);
            $table->dropColumn(['customer_id', 'order_id', 'request_type', 'reference_no', 'request_details']);
        });
    }
};
