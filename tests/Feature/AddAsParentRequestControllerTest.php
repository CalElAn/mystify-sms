<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AddAsParentRequest;

class AddAsParentRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a student cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'student')->first())
            ->get(route('add_as_parent_request.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'student')->first())
            ->get(route('add_as_parent_request.form'))
            ->assertInertia(
                fn(Assert $page) => $page->component('AddAsParentRequestForm'),
            );
    }

    /** @test */
    public function the_request_can_be_sent()
    {
        $student = User::where('user_type', 'student')->first();

        $this->actingAs($student)
            ->post(
                route('add_as_parent_request.send_request', [
                    'email' => $student->parents()->first()->email,
                ]),
            )
            ->assertInvalid();

        Notification::fake();

        $userToSendNotifTo = User::factory()->create([
            'default_user_type' => 'parent',
            'user_type' => 'parent',
        ]);

        $this->actingAs($student)
            ->post(
                route('add_as_parent_request.send_request', [
                    'email' => $userToSendNotifTo->email,
                ]),
            )
            ->assertRedirect();

        Notification::assertSentTo($userToSendNotifTo, function (
            AddAsParentRequest $notification,
            $channels,
        ) use ($student) {
            return $notification->child === $student;
        });
    }

    /** @test */
    public function the_request_can_be_accepted()
    {
        $parent = User::where('user_type', 'headteacher')->first();

        $student = User::where('user_type', 'student')->first();

        $this->actingAs($parent)
            ->post(
                route('add_as_parent_request.accept_request', [
                    'childId' => $student->id,
                ]),
            )
            ->assertRedirect();

        $this->assertTrue($parent->fresh()->children->contains($student));
    }
}
