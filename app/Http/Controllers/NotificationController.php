<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Notifications/Index', [
            'notifications' => $request->user()->getNotifications(),
        ]);
    }

    public function viewSchoolNotifications(Request $request)
    {
        $this->authorize('performAdministratorActions', User::class);

        return Inertia::render('Notifications/SchoolNotifications', [
            'notifications' => $request->user()->school->notifications,
        ]);
    }
}
