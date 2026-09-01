<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Find or verify the Head Office branch
        $hoBranch = DB::table('hws_branches')->where('is_head_office', 1)->first();
        if (!$hoBranch) {
            $hoBranchId = DB::table('hws_branches')->insertGetId([
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
        } else {
            $hoBranchId = $hoBranch->id;
        }

        // 2. Backfill super admin user (id = 1 or administrator role) with HO branch_id
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'branch_id')) {
            DB::table('admins')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => $hoBranchId]);
        }

        // 3. Backfill old records in orders, invoices, hws_site_surveys, hws_tasks to HO branch_id
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'branch_id')) {
            DB::table('orders')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => $hoBranchId]);
        }

        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'branch_id')) {
            DB::table('invoices')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => $hoBranchId]);
        }

        if (Schema::hasTable('hws_site_surveys') && Schema::hasColumn('hws_site_surveys', 'branch_id')) {
            DB::table('hws_site_surveys')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => $hoBranchId]);
        }

        if (Schema::hasTable('hws_tasks') && Schema::hasColumn('hws_tasks', 'branch_id')) {
            DB::table('hws_tasks')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => $hoBranchId]);
        }
    }

    public function down(): void
    {
    }
};
