<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_id',
        'user_id',
        'desk_id',
        'status',
        'access_code'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'organization_id' => 'integer',
        'user_id' => 'integer',
        'desk_id' => 'integer',
        'status' => 'boolean'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            //->timezone(config('app.timezone'))
            // Leave this part off if you want to keep the property as
            // a Carbon object rather than always just returning a string
            ->toDateTimeString();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($sale) {
            $sale->organization_id = Auth::user()->organization_id;
            empty($sale->uuid) && $sale->uuid = (string)Str::uuid();
        });
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SaleDetail::class)->with('product');
    }

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function desk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Desk::class);
    }
}
