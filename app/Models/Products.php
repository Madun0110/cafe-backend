<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;

=======

class Products extends Model
{
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'is_available',
<<<<<<< HEAD
        'image',
=======
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
    ];

    protected $casts = [
        'category_id' => 'integer',
<<<<<<< HEAD
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
=======
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
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
    }
}
