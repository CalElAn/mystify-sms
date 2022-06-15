<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use App\Models\NoticeBoard;

class NoticeBoardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a headteacher cannot view the form
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->get(route('notice_board.create'))
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->get(route('notice_board.create'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('NoticeBoard/Create')
                    ->hasAll('term'),
            );
    }

    /** @test */
    public function a_notice_board_message_can_be_stored()
    {
        $input = [
            'message' => 'notice board message',
            'term_id' => 1,
        ];

        // a user who is not a headteacher cannot store an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->post(route('notice_board.store'), $input)
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('notice_board.store'), ['message' => 'test validation'])
            ->assertInvalid();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('notice_board.store'), $input)
            ->assertRedirect();

        $this->assertDatabaseHas('notice_board', $input);
    }

}
