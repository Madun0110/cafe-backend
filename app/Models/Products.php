<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'is_available',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'is_available' => 'boolean',
        'price' => 'float',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
}
