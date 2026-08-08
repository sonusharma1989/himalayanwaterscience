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
        Schema::create('hws_site_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->unique()->constrained('hws_tasks')->cascadeOnDelete();
            $table->enum('property_type', ['hotel', 'hospital', 'bungalow', 'other'])->default('other');
            $table->unsignedInteger('floors')->nullable();
            $table->unsignedInteger('built_up_area_sqft')->nullable();
            $table->unsignedInteger('rooms_units')->nullable();
            $table->decimal('water_use_kld', 8, 2)->nullable();
            $table->enum('water_source', ['municipal', 'borewell', 'tanker', 'river'])->nullable();
            $table->enum('wastewater_disposal', [
                'septic_tank',
                'open_drain',
                'existing_stp',
                'none',
            ])->nullable();
            $table->enum('space_available', [
                'open_area',
                'limited',
                'basement_only',
                'not_sure',
            ])->nullable();
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_site_surveys');
    }
};
