<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'table',
        'total',
    ];

    protected $casts = [
        'table' => 'integer',
        'total' => 'float',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function food()
    {
        return $this->hasMany(FoodOrderDetails::class, 'order_id' , 'id');
    }

    public function drink()
    {
        return $this->hasMany(DrinkOrderDetails::class, 'order_id' , 'id');
    }

}
