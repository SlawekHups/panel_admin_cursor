<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'number',
        'total_gross',
        'total_net',
        'issued_at',
        'pdf_path',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'total_gross' => 'decimal:2',
            'total_net' => 'decimal:2',
            'issued_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    /**
     * Get the order that owns the invoice.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
