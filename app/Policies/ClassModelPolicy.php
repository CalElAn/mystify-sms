<?php

namespace App\Policies;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $authUser)
    {
        if ($authUser->user_type === 'parent') {
            return false;
        }

        return true;
    }

    public function viewForm(User $authUser)
    {
        return $authUser->default_user_type === 'headteacher';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ClassModel $classModel)
    {
        //
    }

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

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $authUser, ClassModel $classModel)
    {
        return $authUser->default_user_type === 'headteacher' &&
            $authUser->school_id === $classModel->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $authUser, ClassModel $classModel)
    {
        return $authUser->default_user_type === 'headteacher' &&
            $authUser->school_id === $classModel->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ClassModel $classModel)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ClassModel $classModel)
    {
        //
    }
}
