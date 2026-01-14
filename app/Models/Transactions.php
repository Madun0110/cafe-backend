<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'total',
        'status',
        'payment_time',
    ];

    protected $casts = [
        'payment_time' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }
}
