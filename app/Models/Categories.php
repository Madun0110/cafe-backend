<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name', // hanya name saja
    ];

    protected $casts = [
        'name'       => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 1 kategori punya banyak produk
     */
    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }
}
