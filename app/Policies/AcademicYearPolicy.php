<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicYearPolicy
{
    use HandlesAuthorization;

    public function viewForm(User $authUser)
    {
        return $authUser->default_user_type === 'headteacher';
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
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $authUser, AcademicYear $academicYear)
    {
        return $authUser->default_user_type === 'headteacher' &&
            $authUser->school_id === $academicYear->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $authUser, AcademicYear $academicYear)
    {
        return $authUser->default_user_type === 'headteacher' &&
            $authUser->school_id === $academicYear->school_id;
    }
}
