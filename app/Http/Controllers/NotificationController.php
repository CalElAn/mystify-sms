<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        //TODO test

        return Inertia::render('Notifications/Index', [
            'notifications' => $request->user()->notifications,
        ]);
    }
}
