<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            // Primary identifiers
            $table->id();
            $table->integer('contract_id')->unique();

            // Overview
            $table->string('in_charge');
            $table->string('project_title');
            $table->string('location');
            $table->string('contractor');
            $table->decimal('original_contract_amount', 15, 2)->nullable();
            $table->date('date_started');
            $table->integer('contract_days')->nullable();
            $table->date('original_contract_expiry');
            $table->date('revised_contract_expiry')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'expiring', 'expired'])->default('ongoing');
            $table->date('completed_at')->nullable();

            // Performance
            $table->decimal('as_planned', 5, 2)->default(0);
            $table->decimal('work_done', 5, 2)->default(0);
            $table->decimal('slippage', 5, 2)->default(0);
            $table->string('progress_updated_at')->nullable();
            $table->decimal('ld_accomplished', 5, 3)->nullable();
            $table->decimal('ld_unworked', 5, 2)->nullable();
            $table->integer('ld_days_overdue')->nullable();
            $table->decimal('ld_per_day', 15, 2)->nullable();
            $table->decimal('total_ld', 15, 2)->nullable();

            // Extensions
            $table->json('documents_pressed')->nullable();
            $table->integer('time_extension')->nullable();      // count of TE entries
            $table->json('extension_days')->nullable();         // TE days per extension
            $table->json('cost_involved')->nullable();          // TE cost per extension
            $table->json('date_requested')->nullable();         // request dates for TE/VO entries
            $table->integer('variation_order')->nullable();     // count of VO entries
            $table->json('vo_days')->nullable();                // VO days per variation
            $table->json('vo_cost')->nullable();                // VO cost per variation
            $table->integer('suspension_days')->nullable();     // total SO days

            // Billing
            $table->json('billing_amounts')->nullable();        // billing update amounts
            $table->json('billing_dates')->nullable();          // billing update dates
            $table->decimal('remaining_balance', 15, 2)->nullable();
            $table->decimal('total_amount_billed', 15, 2)->nullable();
            $table->decimal('advance_billing_pct', 5, 2)->nullable();
            $table->decimal('advance_billing_amount', 15, 2)->nullable();
            $table->decimal('retention_pct', 5, 2)->nullable();
            $table->decimal('retention_amount', 15, 2)->nullable();

            // Admin
            $table->json('issuances')->nullable();
            $table->date('performance_bond_date')->nullable();   // performance bond expiry
            $table->text('remarks_recommendation')->nullable();

            // Metadata
            $table->timestamps();
        });

        Schema::create('project_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->json('changes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_logs');
        Schema::dropIfExists('projects');
    }
};