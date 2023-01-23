<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'active',
        'show_menu'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'active' => 'boolean',
        'show_menu' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($organization) {
            empty($organization->uuid) && $organization->uuid = (string)Str::uuid();
        });
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
