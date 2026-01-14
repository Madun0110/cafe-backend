<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
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
=======

class Categories extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => 'string',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function products()
    {
        return $this->hasMany(Products::class);
    }
}
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
