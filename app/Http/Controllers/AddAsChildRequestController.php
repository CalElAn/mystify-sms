<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use App\Notifications\AddAsChildRequest;

class AddAsChildRequestController extends Controller
{
    public function form()
    {
        //TODO authorize
        //TODO test

        return Inertia::render('AddAsChildRequest/Form');
    }

    public function sendRequest(Request $request)
    {
        //TODO authorize
        //TODO test

        $request->validate([
            'email' => [
                'required',
                Rule::exists('users')->where(
                    fn($query) => $query->where([
                        ['default_user_type', 'student'],
                        ['email', $request->email],
                    ]),
                ),
            ],
        ]);

        User::where('email', $request->email)
            ->first()
            ->notify(new AddAsChildRequest($request->user()));

        return back();
    }

    public function acceptRequest(Request $request)
    {
        //TODO test
        $user = $request->user();

        if ($user->parents->contains(User::find($request->parentId))) {
            return $this->declineRequest($request);
        }

        $user->parents()->attach($request->parentId);

        return back();
    }

    public function declineRequest(Request $request)
    {
        //TODO test
        DatabaseNotification::find($request->notificationId)->delete();

        return back();
    }
}
