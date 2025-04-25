<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\Travel;
use App\Utilities\Numbers;

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
        static::creating(function ($model){
            $model->user_id = Auth::id();
        });
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => Numbers::getAttributeNumberValue($value),
            set: fn (int|float|string $value) => Numbers::setAttributeNumberValue($value)
        );
    }

}