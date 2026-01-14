<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount_paid', 12, 2)->after('payment_method');
            $table->decimal('change', 12, 2)->default(0)->after('total');
            $table->string('status')->default('paid')->after('change');
            $table->timestamp('payment_time')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'amount_paid',
                'change',
                'status',
                'payment_time',
            ]);
        });
    }
};
