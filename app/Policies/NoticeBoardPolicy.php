<?php

namespace App\Policies;

use App\Models\NoticeBoard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoticeBoardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $authUser)
    {
        return $authUser->default_user_type === 'headteacher';
    }
}
