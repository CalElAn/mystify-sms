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
        $this->authorize('performStudentActions', User::class);

        return Inertia::render('AddAsParentRequestForm');
    }

    public function sendRequest(Request $request)
    {
        $this->authorize('performStudentActions', User::class);

        $request->validate([
            'email' => [
                'required',
                Rule::exists('users')->where(
                    fn($query) => $query->where([
                        ['email', $request->email],
                        ['default_user_type', '<>', 'student'],
                    ]),
                ),
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request
                            ->user()
                            ->parents->contains(
                                User::where('email', $request->email)->first(),
                            )
                    ) {
                        $fail(
                            $value .
                                ' has already been added as one of your parents',
                        );
                    }
                },
            ],
        ]);

        User::where('email', $request->email)
            ->first()
            ->notify(new AddAsParentRequest($request->user()));

        return back();
    }

    public function acceptRequest(Request $request)
    {
        $user = $request->user();

        if ($user->children->contains(User::find($request->childId))) {
            return $this->deleteRequest($request);
        }

        $user->children()->attach($request->childId);

        return $this->deleteRequest($request);
    }

    public function declineRequest(Request $request)
    {
        return $this->deleteRequest($request);
    }

    public function deleteRequest(Request $request)
    {
        DatabaseNotification::find($request->notificationId)?->delete();

        return back();
    }
}
