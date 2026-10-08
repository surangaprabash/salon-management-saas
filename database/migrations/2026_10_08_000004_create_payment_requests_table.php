<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('billing_record_id');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50);
            $table->string('reference_number', 100)->nullable();
            $table->text('note')->nullable();

            // Stored on a private disk under a generated name.
            $table->string('receipt_path');
            $table->string('receipt_original_name');
            $table->string('receipt_mime', 100);
            $table->unsignedInteger('receipt_size');

            $table->string('status', 20)->default('PENDING');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            // The bill must belong to the same salon as the request.
            $table->foreign(['billing_record_id', 'salon_id'])
                ->references(['id', 'salon_id'])
                ->on('billing_records')
                ->restrictOnDelete();

            $table->index('billing_record_id');
            $table->index(['status', 'created_at']);
            $table->index(['salon_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};