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
        if (Schema::hasTable('hws_notifications')) {
            return;
        }

        Schema::create('hws_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['admin_id', 'is_read']);
            $table->foreign('admin_id')->references('id')->on('admins')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_notifications');
    }
};
