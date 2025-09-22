<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'sku',
        'qty',
        'price_gross',
        'price_net',
        'tax_rate',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'price_gross' => 'decimal:2',
            'price_net' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'payload' => 'array',
        ];
    }

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
