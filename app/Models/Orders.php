<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    /**
     * Kolom yang boleh di–mass assign (sesuai struktur DB kamu sekarang).
     *
     * Tabel orders (versi sekarang):
     *  - id
     *  - table           (nomor meja, string / int)
     *  - customer_name
     *  - customer_phone
     *  - items           (JSON)
     *  - total           (grand total)
     *  - status          (pending, processing, ready, completed, cancelled)
     *  - payment_status  (unpaid / paid)  → dari contoh JSON API
     *  - created_at
     *  - updated_at
     */
    protected $fillable = [
        'table',
        'customer_name',
        'customer_phone',
        'items',
        'total',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'items'        => 'array',   // JSON → array PHP
        'total'        => 'float',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Relasi ke transaksi pembayaran (tabel transactions).
     */
    public function transaction()
    {
        return $this->hasOne(Transactions::class, 'order_id');
    }
}
