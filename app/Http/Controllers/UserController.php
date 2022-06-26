<?php

namespace App\Http\Controllers;

use App\Models\ParentStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * Show all requested user types for school.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        /** @var \App\Models\School */
        $school = $request->user()->school;
        $userType = $request->userType;

        switch ($userType) {
            case 'students':
                $this->authorize('viewStudents', User::class);
                $query = $school->users()->studentScope();
                break;

            case 'parents':
                $this->authorize('viewParents', User::class);
                //not using parent scope cos for eg a teacher can have a child
                $query = User::whereHas(
                    'children',
                    fn(Builder $query) => $query->where(
                        'school_id',
                        $school->id,
                    ),
                );
                break;

            case 'teachers':
                $this->authorize('viewTeachers', User::class);
                $query = $school->users()->teacherScope();
                break;

            case 'administrators':
                $this->authorize('viewAdministrators', User::class);
                $query = $school->users()->administratorScope();
                break;

            default:
                abort(404);
                break;
        }

        if ($request->nameFilter) {
            $query->where('name', 'LIKE', "%{$request->nameFilter}%");
        }

        return Inertia::render('Users/Index', [
            'users' => $query
                ->orderBy('name')
                ->paginate(10)
                ->through(function ($user) use ($userType) {
                    if ($userType === 'teachers') {
                        // $user->append('unique_subjects');
                    }
                    return $user;
                })
                ->withQueryString(),
            'userType' => $userType,
            'nameFilter' => $request->nameFilter,
        ]);
    }

    /**
     * change a user's "user_type" field
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function changeUserType(Request $request)
    {
        $this->authorize('changeUserType', User::class);

        /** @var \App\Models\User */
        $user = Auth::user();

        $user->update([
            'user_type' => $request->user_type,
        ]);

        return \Redirect::route('dashboard');
    }

    public function removeChildrenForm(Request $request)
    {
        $this->authorize('performParentActions', User::class);

        return Inertia::render('RemoveChildrenForm', [
            'children' => $request->user()->children,
        ]);
    }

    public function deleteChild(ParentStudent $parentStudent)
    {
        //i feel its not necessary to create a whole policy for ParentStudent just to authorize that
        //user id is equal to parent id
        $this->authorize('performParentActions', User::class);

        $parentStudent->delete();

        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
