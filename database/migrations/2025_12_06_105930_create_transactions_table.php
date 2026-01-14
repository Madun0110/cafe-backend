<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->string('payment_method', 50); // contoh: cash, qris, transfer
            $table->decimal('total', 12, 2);      // total yang dibayar sesuai order

            // Kalau nanti pengin lebih advanced, bisa tambah:
            // $table->decimal('amount_paid', 12, 2)->nullable();
            // $table->decimal('change', 12, 2)->nullable();
            // $table->string('status', 20)->default('paid'); // paid / refunded
            // $table->timestamp('payment_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
