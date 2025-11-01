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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference', 100)->unique();
            $table->string('paystack_reference', 100)->unique()->nullable();
            $table->bigInteger('amount');
            $table->string('currency', 3)->default('NGN');
            $table->enum('status', ['pending', 'success', 'failed', 'abandoned'])->default('pending');
            $table->string('payment_channel', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('authorization_code')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('webhook_processed')->default(false);
            $table->json('paystack_response')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('webhook_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
