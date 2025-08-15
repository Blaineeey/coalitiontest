<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'total_value'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    // Calculate total value automatically
    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->total_value = $product->quantity * $product->price;
        });

        static::updating(function ($product) {
            $product->total_value = $product->quantity * $product->price;
        });
    }
}
