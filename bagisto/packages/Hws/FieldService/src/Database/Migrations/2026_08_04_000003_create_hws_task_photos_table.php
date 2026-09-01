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
        if (!Schema::hasTable('hws_task_photos')) {
            Schema::create('hws_task_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('hws_tasks')->cascadeOnDelete();
                $table->enum('type', ['before', 'after', 'survey_site']);
                $table->string('file_path');
                $table->timestamps();

                $table->index(['task_id', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_task_photos');
    }
};
