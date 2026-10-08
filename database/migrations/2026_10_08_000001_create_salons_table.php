<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->text('description')->nullable();

            $table->string('status', 20)->default('PENDING')->index();
            $table->text('blocked_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();

            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->char('currency', 3)->default('LKR');
            $table->date('billing_start_date')->nullable();
            $table->date('next_billing_date')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salons');
    }
};