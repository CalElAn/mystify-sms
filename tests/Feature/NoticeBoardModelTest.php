<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\NoticeBoard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoticeBoardModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_notice_board_message_has_a_time_string()
    {
        $noticeBoard = NoticeBoard::factory()->create();

        $this->assertEquals(Carbon::create($noticeBoard->created_at)->format('h:i A'), $noticeBoard->time_string);
    }

    /** @test */
    public function a_notice_board_message_belongs_to_a_user()
    {
        $noticeBoard = NoticeBoard::factory()->create();

        $this->assertInstanceOf('App\Models\User', $noticeBoard->user);
    }
}
