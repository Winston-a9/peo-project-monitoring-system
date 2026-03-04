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
            $table->date('original_contract_expiry');
            $table->date('revised_contract_expiry')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'expired'])->default('ongoing');
            $table->date('completed_at')->nullable();
            $table->decimal('contract_amount', 15, 2)->default(0);
            $table->decimal('as_planned', 5, 2)->default(0);
            $table->decimal('work_done', 5, 2)->default(0);
            $table->decimal('slippage', 5, 2)->default(0);
            $table->text('remarks_recommendation')->nullable();
            $table->json('issuances')->nullable();
            $table->json('documents_pressed')->nullable();
            $table->string('time_extension')->nullable();
            $table->json('extension_days')->nullable();
            $table->timestamps();
        });
        Schema::create('project_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');        // 'updated', 'created', 'status_changed', etc.
            $table->json('changes');         // what changed: old vs new values
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};