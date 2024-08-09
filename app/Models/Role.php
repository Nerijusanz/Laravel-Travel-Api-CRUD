<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    public const ADMIN = 'admin';
    public const USER = 'user';

    protected $fillable = ['name'];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->as('role_user');
    }

    public function scopeAdmin($query)
    {
        return $query->where('name', self::ADMIN)->pluck('id');
    }

    public function scopeUser($query)
    {
        return $query->where('name', self::USER)->pluck('id');
    }

}
