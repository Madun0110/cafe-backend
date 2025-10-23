<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'order_id',
        'payment_method',
        'total',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'payment_method' => 'string',
        'total' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }
}
