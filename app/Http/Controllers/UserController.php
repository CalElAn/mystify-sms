<?php

namespace App\Http\Controllers;

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
     * @return \Illuminate\Http\Response
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
                $query = User::parentScope()->whereHas(
                    'children',
                    fn(Builder $query) => $query->where(
                        'school_id',
                        $school->id,
                    ),
                );
                break;

            case 'teachers':
                $this->authorize('viewTeachers', User::class);
                $query = $school
                    ->users()
                    ->teacherScope()
                    ->with('subjects');
                break;

            case 'administrators':
                $this->authorize('viewAdministrators', User::class);
                $query = $school->users()->administratorScope();
                break;

            default:
                # code...
                break;
        }

        if ($request->nameFilter) {
            $query->where('name', 'LIKE', "%{$request->nameFilter}%");
        }

        return Inertia::render('Users/Index', [
            'school' => $school,
            'showTerm' => false,
            'users' => $query
                ->orderBy('name')
                ->paginate(10)
                ->through(function ($user) use ($userType) {
                    if ($userType === 'teachers') {
                        $user->append('unique_subjects');
                    }
                    return $user;
                })
                ->withQueryString(),
            'userType' => $userType,
            'nameFilter' => $request->nameFilter,
        ]);
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
