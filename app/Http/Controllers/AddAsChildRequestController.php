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
        $this->authorize('performParentActions', User::class);

        return Inertia::render('AddAsChildRequestForm');
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                Rule::exists('users')->where(
                    fn($query) => $query->where([
                        ['default_user_type', 'student'],
                        ['email', $request->email],
                    ]),
                ),
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request
                            ->user()
                            ->children->contains(
                                User::where('email', $request->email)->first(),
                            )
                    ) {
                        $fail($value . ' has already been added as one of your children');
                    }
                },
            ],
        ]);

        User::where('email', $request->email)
            ->first()
            ->notify(new AddAsChildRequest($request->user()));

        return back();
    }

    public function acceptRequest(Request $request)
    {
        $user = $request->user();

        if ($user->parents->contains(User::find($request->parentId))) {
            return $this->deleteRequest($request);
        }

        $user->parents()->attach($request->parentId);

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
