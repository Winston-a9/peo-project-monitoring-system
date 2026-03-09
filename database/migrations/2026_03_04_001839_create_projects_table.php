<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('in_charge');
            $table->string('project_title');
            $table->string('location');
            $table->string('contractor');
            $table->date('date_started');
            $table->integer('contract_days')->nullable();
            $table->date('original_contract_expiry');
            $table->date('revised_contract_expiry')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'expired'])->default('ongoing');
            $table->date('completed_at')->nullable();
            $table->decimal('contract_amount', 15, 2)->default(0);
            $table->decimal('as_planned', 5, 2)->default(0);
            $table->decimal('work_done', 5, 2)->default(0);
            $table->decimal('slippage', 5, 2)->default(0);
            $table->text('remarks_recommendation')->nullable();

            // Documents & Extensions
            $table->json('issuances')->nullable();
            $table->json('documents_pressed')->nullable();
            $table->integer('time_extension')->nullable();      // count of TE entries
            $table->json('extension_days')->nullable();         // parallel TE days array
            $table->json('cost_involved')->nullable();          // parallel TE cost array
            $table->integer('suspension_days')->nullable();     // total SO days (single accumulating int)
            $table->integer('variation_order')->nullable();     // count of VO entries
            $table->json('vo_days')->nullable();                // parallel VO days array
            $table->json('vo_cost')->nullable();                // parallel VO cost array

            // Liquidated Damages
            $table->decimal('ld_accomplished', 5, 2)->nullable();
            $table->decimal('ld_unworked', 5, 2)->nullable();
            $table->decimal('ld_per_day', 15, 2)->nullable();
            $table->decimal('total_ld', 15, 2)->nullable();
            $table->integer('ld_days_overdue')->nullable();     // integer, NOT date

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