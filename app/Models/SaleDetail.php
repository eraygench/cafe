<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'organization_id' => 'integer',
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function sale(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
