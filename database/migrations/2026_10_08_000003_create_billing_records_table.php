<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->restrictOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('LKR');
            $table->string('status', 20)->default('PENDING');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // One bill per salon per month.
            $table->unique(['salon_id', 'period_start']);
            // Target for payment_requests composite FK (guarantees ownership).
            $table->unique(['id', 'salon_id']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_records');
    }
};