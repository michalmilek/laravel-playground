<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'quantity', 'status', 'slug'
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = self::createUniqueSlug($product->name);
        });

        static::updating(function ($product) {
            $product->slug = self::createUniqueSlug($product->name, $product->id);
        });
    }

    private static function createUniqueSlug($name, $id = 0): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

}
