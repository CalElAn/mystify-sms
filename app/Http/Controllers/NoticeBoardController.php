<?php

namespace App\Http\Controllers;

use App\Models\NoticeBoard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeBoardController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->user()->school;
        $term = $school->getTerm($request);

        return Inertia::render('NoticeBoard/Index', [
            'noticeBoardMessages' => $school->getNoticeBoardMessages($term->id),
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', NoticeBoard::class);

        return Inertia::render('NoticeBoard/Create', [
            'currentTerm' => $request
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
