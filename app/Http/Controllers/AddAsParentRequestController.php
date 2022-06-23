<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use App\Notifications\AddAsParentRequest;

class AddAsParentRequestController extends Controller
{
    public function form()
    {
        //TODO authorize
        //TODO test

        return Inertia::render('AddAsParentRequestForm');
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
                        ['email', $request->email],
                    ]),
                ),
            ],
        ]);

        User::where('email', $request->email)
            ->first()
            ->notify(new AddAsParentRequest($request->user()));

        return back();
    }

    public function acceptRequest(Request $request)
    {
        //TODO test
        $user = $request->user();

        if ($user->children->contains(User::find($request->childId))) {
            return $this->declineRequest($request);
        }

        $user->children()->attach($request->childId);

        return back();
    }

    public function declineRequest(Request $request)
    {
        //TODO test
        DatabaseNotification::find($request->notificationId)->delete();

        return back();
    }
}
