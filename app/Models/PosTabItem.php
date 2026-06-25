<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTabItem extends Model
{
    protected $primaryKey = 'tab_item_id';

    public $timestamps = false;

    protected $fillable = [
        'tab_id',
        'product_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function tab(): BelongsTo
    {
        return $this->belongsTo(PosTab::class, 'tab_id', 'tab_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PosProduct::class, 'product_id', 'product_id');
    }
}
