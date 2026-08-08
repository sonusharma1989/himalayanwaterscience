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
        Schema::create('hws_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_no')->unique();
            $table->enum('type', [
                'installation',
                'amc_service',
                'complaint',
                'service',
                'sales_visit',
                'site_survey',
            ]);
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->enum('priority', ['urgent', 'high', 'normal', 'low'])->default('normal');

            // 0 = Assign, 1 = Accept, 2 = Travel, 3 = Work, 4 = Done
            $table->unsignedTinyInteger('step')->default(0);

            $table->dateTime('scheduled_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('work_description')->nullable();
            $table->string('signature_path')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->timestamps();

            $table->index(['type', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_tasks');
    }
};
