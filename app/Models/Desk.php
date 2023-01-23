<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Desk extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_id',
        'organization_id',
        'name',
        'active'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'organization_id' => 'integer',
        'section_id' => 'integer',
        'active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($section) {
            $section->organization_id = Auth::user()->organization_id;
        });
    }

    public function sale(): Model|null
    {
        return $this->hasMany(Sale::class)->with('details')->firstWhere('status', '0');
    }

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sale::class)->with('details');
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
