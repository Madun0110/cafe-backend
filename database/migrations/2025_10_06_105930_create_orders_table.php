<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // info pelanggan + meja
            $table->string('table');           // nomor meja, contoh: "A1", "2", dst
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20);

            // items dalam bentuk JSON
            // contoh isi:
            // [
            //   { "product_id":1, "product_name":"Americano", "quantity":2, "price":15000, "total":30000, ... },
            //   ...
            // ]
            $table->json('items')->nullable();

            // total harga seluruh order
            $table->decimal('total', 12, 2)->default(0);

            // status order: pending, processing, ready, completed, cancelled
            $table->string('status', 20)->default('pending');

            // status pembayaran: unpaid, paid, refunded (opsional, tapi berguna)
            $table->string('payment_status', 20)->default('unpaid');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
