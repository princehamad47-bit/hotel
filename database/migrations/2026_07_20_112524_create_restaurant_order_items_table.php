<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();

            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();

            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            $table->enum('order_type', ['in_house', 'walk_in', 'room_service', 'takeaway'])->default('walk_in');
            $table->string('table_number')->nullable();

            $table->enum('status', ['pending', 'preparing', 'served', 'completed', 'cancelled'])->default('pending');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_orders');
    }
};
