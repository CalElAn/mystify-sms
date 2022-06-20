<?php

namespace App\Policies;

use App\Models\ClassTeacher;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassTeacherPolicy
{
    use HandlesAuthorization;

    public function viewForm(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassTeacher  $classTeacher
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $authUser)
    {
        return $authUser->user_type === 'teacher';
    }
}
