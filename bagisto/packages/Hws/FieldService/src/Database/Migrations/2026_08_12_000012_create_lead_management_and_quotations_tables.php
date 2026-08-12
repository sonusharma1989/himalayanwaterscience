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
        // 1. Modify hws_site_surveys status enum and add new CRM columns
        DB::statement("ALTER TABLE hws_site_surveys MODIFY COLUMN status ENUM('draft', 'submitted', 'new', 'contacted', 'proposal_sent', 'negotiation', 'won', 'lost') DEFAULT 'draft'");

        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->enum('temperature', ['hot', 'warm', 'cold'])->default('warm')->after('status');
            $table->string('source')->nullable()->after('temperature');
            $table->unsignedInteger('assigned_to')->nullable()->after('source');
            $table->dateTime('next_follow_up_at')->nullable()->after('assigned_to');

            $table->foreign('assigned_to')->references('id')->on('admins')->onDelete('set null');
        });

        // 2. Create hws_lead_activities table
        Schema::create('hws_lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('hws_site_surveys')->cascadeOnDelete();
            $table->unsignedInteger('action_by');
            $table->enum('activity_type', ['call', 'email', 'meeting', 'note', 'whatsapp'])->default('note');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('action_by')->references('id')->on('admins')->cascadeOnDelete();
        });

        // 3. Create hws_quotations table
        Schema::create('hws_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('hws_site_surveys')->nullOnDelete();
            $table->string('quote_no')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->timestamps();
        });

        // 4. Create hws_quotation_items table
        Schema::create('hws_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('hws_quotations')->cascadeOnDelete();
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('rate', 12, 2)->default(0.00);
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hws_quotation_items');
        Schema::dropIfExists('hws_quotations');
        Schema::dropIfExists('hws_lead_activities');

        Schema::table('hws_site_surveys', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['customer_email', 'temperature', 'source', 'assigned_to', 'next_follow_up_at']);
        });

        DB::statement("ALTER TABLE hws_site_surveys MODIFY COLUMN status ENUM('draft', 'submitted') DEFAULT 'draft'");
    }
};
