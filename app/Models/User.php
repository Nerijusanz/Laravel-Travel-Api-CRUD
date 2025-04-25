<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Role;
use App\Models\Travel;
use App\Models\Tour;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->as('role_user');
    }

    public function travels(): HasMany
    {
        return $this->hasMany(Travel::class);
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function scopeAdminRole(Builder $query)
    {
        return $query->whereHas('roles',function($query) {
            $query->where('name',Role::ADMIN);
        })->first();
    }

    public function scopeUserRole(Builder $query)
    {
        return $query->whereHas('roles',function($query) {
            $query->where('name',Role::USER);
        })->first();
    }

}