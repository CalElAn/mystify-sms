<?php

namespace App\Policies;

use App\Models\Term;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermPolicy
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
     * @param  \App\Models\Term  $term
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $authUser, Term $term)
    {
        return $authUser->default_user_type === 'headteacher' &&
            $authUser->school_id === $term->academicYear->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Term  $term
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $authUser, Term $term)
    {
        return $authUser->default_user_type === 'headteacher' &&
            $authUser->school_id === $term->academicYear->school_id;
    }
}
