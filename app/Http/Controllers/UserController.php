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
        //TODO write test when done with admin portions and functions (cos )
        /** @var \App\Models\School */
        $school = Auth::user()->school;
        $userType = $request->userType;

        switch ($userType) {
            case 'students':
                $query = $school->users()->studentScope();
                break;

            case 'parents':
                $query = User::parentScope()->whereHas(
                    'children',
                    fn(Builder $query) => $query->where(
                        'school_id',
                        $school->id,
                    ),
                );
                break;

            case 'teachers':
                $query = $school->users()->teacherScope();
                break;

            case 'administrators':
                $query = $school->users()->administratorScope();
                break;

            default:
                # code...
                break;
        }

        if ($request->nameFilter) {
            $query->where('name', 'LIKE', "%{$request->nameFilter}%");
        }

        $query->orderBy('name');

        return Inertia::render('User/Index', [
            'school' => $school,
            'showTerm' => false,
            'users' => $query
                // ->orderBy('name')
                ->paginate(10)
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
