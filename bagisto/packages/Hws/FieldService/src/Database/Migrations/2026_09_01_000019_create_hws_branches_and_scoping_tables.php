<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create hws_branches table
        if (!Schema::hasTable('hws_branches')) {
            Schema::create('hws_branches', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('gstin')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('pincode')->nullable();
                $table->boolean('is_head_office')->default(0);
                $table->boolean('status')->default(1);
                $table->timestamps();
            });

            // Insert default Head Office branch
            DB::table('hws_branches')->insert([
                'code'           => 'HO-MAIN',
                'name'           => 'Head Office (Main Branch)',
                'phone'          => '+91-9876543210',
                'email'          => 'ho@himalayanwaterscience.com',
                'gstin'          => '05AAAAA0000A1Z5',
                'address'        => 'Industrial Area, Phase 1',
                'city'           => 'Dehradun',
                'state'          => 'Uttarakhand',
                'pincode'        => '248001',
                'is_head_office' => 1,
                'status'         => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $defaultBranch = DB::table('hws_branches')->first();
        $defaultBranchId = $defaultBranch ? $defaultBranch->id : 1;

        // 2. Add branch_id to admins
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'branch_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('role_id');
            });
        }

        // 3. Add branch_id to orders
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'branch_id')) {
            Schema::table('orders', function (Blueprint $table) use ($defaultBranchId) {
                $table->unsignedBigInteger('branch_id')->nullable()->default($defaultBranchId)->after('channel_name');
            });
        }

        // 4. Add branch_id to invoices
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'branch_id')) {
            Schema::table('invoices', function (Blueprint $table) use ($defaultBranchId) {
                $table->unsignedBigInteger('branch_id')->nullable()->default($defaultBranchId)->after('order_id');
            });
        }

        // 5. Add branch_id to hws_leads
        if (Schema::hasTable('hws_leads') && !Schema::hasColumn('hws_leads', 'branch_id')) {
            Schema::table('hws_leads', function (Blueprint $table) use ($defaultBranchId) {
                $table->unsignedBigInteger('branch_id')->nullable()->default($defaultBranchId)->after('source');
            });
        }

        // 6. Add branch_id to hws_service_requests
        if (Schema::hasTable('hws_service_requests') && !Schema::hasColumn('hws_service_requests', 'branch_id')) {
            Schema::table('hws_service_requests', function (Blueprint $table) use ($defaultBranchId) {
                $table->unsignedBigInteger('branch_id')->nullable()->default($defaultBranchId)->after('request_no');
            });
        }

        // 7. Add branch_id to hws_tasks
        if (Schema::hasTable('hws_tasks') && !Schema::hasColumn('hws_tasks', 'branch_id')) {
            Schema::table('hws_tasks', function (Blueprint $table) use ($defaultBranchId) {
                $table->unsignedBigInteger('branch_id')->nullable()->default($defaultBranchId)->after('task_no');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hws_branches');
    }
};
