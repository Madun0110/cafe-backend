<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'is_available',
        'image',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'name'        => 'string',
        'description' => 'string',
        'price'       => 'float',
        'is_available'=> 'boolean',
        'image'       => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Relasi: product milik satu kategori
     */
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    /**
     * Auto-append URL gambar penuh -> image_url
     */
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }
}
