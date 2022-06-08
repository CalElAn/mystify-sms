<?php

namespace App\Policies;

use App\Models\User;
use App\Models\School;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the headteacher dashboard.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewHeadteacherDashboard(User $authUser, User $headteacher)
    {
        return $authUser->id === $headteacher->id;
    }

    public function viewStudentDashboard(User $authUser, User $student)
    {
        if ($authUser->id === $student->id) {
            return true;
        }

        if ($authUser->user_type === 'parent') {
            if ($authUser->children->contains($student)) {
                return true;
            }
            return false;
        }

        return $authUser->user_type !== 'student' &&
            $authUser->school_id === $student->school_id;
    }

    public function viewTeacherDashboard(User $authUser, User $teacher)
    {
        return $authUser->id === $teacher->id;
    }

    public function viewParentDashboard(User $authUser, User $parent)
    {       
        if ($authUser->parents->contains($parent)) {
            return true;
        }

        return $authUser->id === $parent->id;
    }

    public function viewStudents(User $authUser)
    {
        if ($authUser->user_type === 'parent') {
            return false;
        }

        return true;
    }

    public function viewClasses(User $authUser)
    {
        if ($authUser->user_type === 'parent') {
            return false;
        }

        return true;
    }

    public function viewParents(User $authUser)
    {
        if (
            $authUser->user_type === 'parent' ||
            $authUser->user_type === 'student'
        ) {
            return false;
        }

        return true;
    }

    public function viewTeachers(User $authUser)
    {
        if ($authUser->user_type === 'parent') {
            return false;
        }

        return true;
    }

    public function viewAdministrators(User $authUser)
    {
        if ($authUser->user_type === 'parent') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        //
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
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
