<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ld_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->date('month'); // stored as first day of the month, e.g. 2026-07-01
            $table->decimal('ld_accomplished', 5, 2)->nullable();
            $table->decimal('ld_unworked', 5, 2)->nullable();
            $table->decimal('ld_per_day', 12, 2)->nullable();
            $table->unsignedInteger('days_overdue')->default(0);
            $table->decimal('ld_amount', 14, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // one snapshot per project per month
            $table->unique(['project_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ld_histories');
    }
};