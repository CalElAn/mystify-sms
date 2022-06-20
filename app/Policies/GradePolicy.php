<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy
{
    use HandlesAuthorization;

    public function viewForm(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }

    public function upsert(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }
}
