<?php

namespace App\Policies;

use App\Models\ClassStudent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassStudentPolicy
{
    use HandlesAuthorization;

    public function viewForm(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }

    public function viewJoinClassForm(User $authUser)
    {
        return $authUser->default_user_type === 'student';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassStudent  $classStudent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }
}
