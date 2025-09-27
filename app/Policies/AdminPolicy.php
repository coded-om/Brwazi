<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function access(Admin $user): bool
    {
        return true; // Adjust later for roles
    }
}
