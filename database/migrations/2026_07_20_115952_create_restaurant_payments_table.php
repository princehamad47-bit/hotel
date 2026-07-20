<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_order_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer']);
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('paid');
            $table->string('transaction_ref')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_payments');
    }
};
