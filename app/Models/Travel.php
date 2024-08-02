<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Tour;
use Illuminate\Support\Facades\Auth;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Travel extends Model
{
    use HasFactory,Sluggable;

    protected $table = 'travels';

    protected $fillable = [
        'user_id',
        'is_public',
        'name',
        'slug',
        'number_of_days',
        'number_of_nights',
        'description',
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
/*
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->user_id = Auth::id();
        });
    }
*/
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function scopeIsPublic()
    {
        return $this->is_public;
    }

    public function scopeIsNotPublic()
    {
        return $this->is_public == 0 ? true : false;
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', 1 );
    }


}
