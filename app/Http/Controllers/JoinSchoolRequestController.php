<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\AcceptedJoinSchoolRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\JoinSchoolRequest;
use Illuminate\Support\Facades\Validator;

class JoinSchoolRequestController extends Controller
{
    public function form(Request $request)
    {
        if ($request->user()->school) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('JoinSchoolRequestForm');
    }

    public function sendRequest(Request $request)
    {
        $schoolName = $request->name;

        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'exists:schools'],
            ],
            [
                'name.exists' => $schoolName . ' does not exist',
            ],
        )->validate();

        School::where('name', $schoolName)
            ->first()
            ->notify(new JoinSchoolRequest($request->user()));

        return back();
    }

    public function acceptRequest(Request $request)
    {
        $userToJoinSchool = User::find($request->userId);

        if ($userToJoinSchool->school_id === $request->user()->school_id) {
            return $this->deleteRequest($request);
        }

        $userToJoinSchool->school()->associate($request->user()->school);
        $userToJoinSchool->save();

        $userToJoinSchool->notify(new AcceptedJoinSchoolRequest());

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
