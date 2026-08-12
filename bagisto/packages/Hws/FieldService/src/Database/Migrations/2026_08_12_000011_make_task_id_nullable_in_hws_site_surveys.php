<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign key constraint first
        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropUnique(['task_id']);
        });

        // 2. Make the column nullable and add customer details directly to surveys table
        DB::statement('ALTER TABLE hws_site_surveys MODIFY task_id BIGINT UNSIGNED NULL');

        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('task_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('customer_address')->nullable()->after('customer_phone');
            $table->json('photos')->nullable()->after('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_phone', 'customer_address', 'photos']);
        });

        DB::statement('ALTER TABLE hws_site_surveys MODIFY task_id BIGINT UNSIGNED NOT NULL');

        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->unique('task_id');
            $table->foreign('task_id')->references('id')->on('hws_tasks')->onDelete('cascade');
        });
    }
};
