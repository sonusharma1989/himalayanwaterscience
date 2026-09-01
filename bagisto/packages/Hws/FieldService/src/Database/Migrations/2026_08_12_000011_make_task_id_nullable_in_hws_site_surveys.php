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
        // 1. Drop foreign key constraint safely if exists
        try {
            Schema::table('hws_site_surveys', function (Blueprint $table) {
                $table->dropForeign(['task_id']);
                $table->dropUnique(['task_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key was already dropped
        }

        // 2. Make the column nullable and add customer details directly to surveys table
        try {
            DB::statement('ALTER TABLE hws_site_surveys MODIFY task_id BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {}

        Schema::table('hws_site_surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('hws_site_surveys', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('task_id');
            }
            if (!Schema::hasColumn('hws_site_surveys', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('hws_site_surveys', 'customer_address')) {
                $table->text('customer_address')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('hws_site_surveys', 'photos')) {
                $table->json('photos')->nullable()->after('follow_up_date');
            }
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
