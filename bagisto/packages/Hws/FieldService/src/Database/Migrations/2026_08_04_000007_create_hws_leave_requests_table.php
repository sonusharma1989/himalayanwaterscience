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
        if (!Schema::hasTable('hws_leave_requests')) {
            Schema::create('hws_leave_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('employee_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedInteger('reviewed_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_leave_requests');
    }
};
