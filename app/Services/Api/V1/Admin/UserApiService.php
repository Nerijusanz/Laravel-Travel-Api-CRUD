<?php

namespace App\Services\Api\V1\Admin;

use App\Models\User;
use App\Models\Role;

class UserApiService{

    public function isAdminRole(User $user): bool
    {
        return $user->roles()->where('name', Role::ADMIN)->exists();
    }

}

?>
