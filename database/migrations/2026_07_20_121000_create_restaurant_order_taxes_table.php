<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_order_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained()->nullOnDelete();

            $table->string('tax_name');
            $table->string('tax_type');
            $table->decimal('tax_value', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_order_taxes');
    }
};
