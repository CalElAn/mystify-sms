<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        //TODO test?
        $authUser = null;

        if (Auth::check()) {
            $authUser = $request->user();
            $authUser->can = [
                'viewStudents' => $authUser->can('viewStudents', $authUser),
                'viewClasses' => $authUser->can('viewClasses', $authUser),
                'viewParents' => $authUser->can('viewParents', $authUser),
                'viewTeachers' => $authUser->can('viewTeachers', $authUser),
                'viewAdministrators' => $authUser->can(
                    'viewAdministrators',
                    $authUser,
                ),
            ];
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $authUser,
            ],
            'ziggy' => function () {
                return (new Ziggy())->toArray();
            },
        ]);
    }
}
