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
        Schema::create('hws_survey_inquiry_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('hws_site_surveys')->cascadeOnDelete();
            $table->enum('inquiry_type', ['stp', 'wtp', 'etp', 'ro_plant', 'softener', 'amc_only']);
            $table->timestamps();

            $table->unique(['survey_id', 'inquiry_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_survey_inquiry_types');
    }
};
