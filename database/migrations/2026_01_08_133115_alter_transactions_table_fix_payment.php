<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            // hapus kolom yang tidak dipakai
            if (Schema::hasColumn('transactions', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }

            if (Schema::hasColumn('transactions', 'change')) {
                $table->dropColumn('change');
            }

            // pastikan kolom total ada
            if (!Schema::hasColumn('transactions', 'total')) {
                $table->decimal('total', 12, 2)->after('payment_method');
            }

            // perbaiki status
            $table->enum('status', ['unpaid', 'paid', 'refunded'])
                  ->default('unpaid')
                  ->change();

            // payment_time boleh null
            $table->timestamp('payment_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->decimal('change', 12, 2)->nullable();
        });
    }
};

