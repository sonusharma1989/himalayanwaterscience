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
        if (!Schema::hasTable('hws_task_materials')) {
            Schema::create('hws_task_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('hws_tasks')->cascadeOnDelete();
                $table->string('name');
                $table->unsignedInteger('quantity')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_task_materials');
    }
};
