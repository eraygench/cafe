<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'organization_id',
        'name',
        'description',
        'image',
        'price',
        'active',
        'show_in_menu'
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
        'price' => 'float',
        'active' => 'boolean',
        'show_in_menu' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($product) {
            $product->organization_id = Auth::user()->organization_id;
        });
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
