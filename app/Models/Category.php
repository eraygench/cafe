<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'organization_id',
        'name',
        'description',
        'image',
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
        'parent_id' => 'integer',
        'organization_id' => 'integer',
        'active' => 'boolean',
        'show_in_menu' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($category) {
            $category->organization_id = Auth::user()->organization_id;
            empty($category->uuid) && $category->uuid = (string)Str::uuid();
        });
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
