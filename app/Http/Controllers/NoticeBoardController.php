<?php

namespace App\Http\Controllers;

use App\Models\NoticeBoard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeBoardController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', NoticeBoard::class);

        return Inertia::render('NoticeBoard/Create', [
            'term' => $request
                ->user()
                ->school->getDefaultTerm()
                ->append('formatted_name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', NoticeBoard::class);

        $request
            ->user()
            ->school->noticeBoard()
            ->create(
                array_merge(
                    $request->validate([
                        'message' => 'required',
                        'term_id' => 'required',
                    ]),
                    ['user_id' => $request->user()->id],
                ),
            );

        return back();
    }
}
