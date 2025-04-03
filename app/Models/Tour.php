<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Travel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Services\Api\V1\TourApiService;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'travel_id',
        'name',
        'price',
        'start_date',
        'end_date',
    ];


    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }


    protected static function booted()
    {

        static::creating(function ($model) {
            $model->user_id = Auth::id();
        });
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => (new TourApiService)::getPriceValue($value),
            set: fn (int|float $value) => (new TourApiService)::setPriceValue($value)
        );
    }


}
