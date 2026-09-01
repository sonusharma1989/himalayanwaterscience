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
        $defaultBranch = DB::table('hws_branches')->where('is_head_office', 1)->first()
            ?: DB::table('hws_branches')->first();
        $defaultBranchId = $defaultBranch ? $defaultBranch->id : 1;

        if (Schema::hasTable('hws_site_surveys') && !Schema::hasColumn('hws_site_surveys', 'branch_id')) {
            Schema::table('hws_site_surveys', function (Blueprint $table) use ($defaultBranchId) {
                $table->unsignedBigInteger('branch_id')->nullable()->default($defaultBranchId)->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('hws_site_surveys') && Schema::hasColumn('hws_site_surveys', 'branch_id')) {
            Schema::table('hws_site_surveys', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
