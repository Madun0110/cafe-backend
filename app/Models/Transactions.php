<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    use HasFactory;
=======

class Transactions extends Model
{
    protected $table = 'transactions';
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d

    protected $fillable = [
        'order_id',
        'payment_method',
        'total',
<<<<<<< HEAD
        'status',
        'payment_time',
    ];

    protected $casts = [
        'payment_time' => 'datetime',
=======
    ];

    protected $casts = [
        'order_id' => 'integer',
        'payment_method' => 'string',
        'total' => 'float',
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }
}
