<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AddAsChildRequest;

class AddAsChildRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a student cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'parent')->first())
            ->get(route('add_as_child_request.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'parent')->first())
            ->get(route('add_as_child_request.form'))
            ->assertInertia(
                fn(Assert $page) => $page->component('AddAsChildRequestForm'),
            );
    }

    /** @test */
    public function the_request_can_be_sent()
    {
        $parent = User::where('user_type', 'parent')->first();

        $this->actingAs($parent)
            ->post(
                route('add_as_child_request.send_request', [
                    'email' => $parent->children()->first()->email,
                ]),
            )
            ->assertInvalid();

        $this->actingAs($parent)
            ->post(
                route('add_as_child_request.send_request', [
                    'email' => User::where(
                        'user_type',
                        '<>',
                        'student',
                    )->first()->email,
                ]),
            )
            ->assertInvalid();

        Notification::fake();

        //creating a new user so there is no chance that it might already be a child of selected parent
        $studentToSendNotifTo = User::factory()->create([
            'default_user_type' => 'student',
            'user_type' => 'student',
        ]);

        $this->actingAs($parent)
            ->post(
                route('add_as_child_request.send_request', [
                    'email' => $studentToSendNotifTo->email,
                ]),
            )
            ->assertRedirect();

        Notification::assertSentTo($studentToSendNotifTo, function (
            AddAsChildRequest $notification,
            $channels,
        ) use ($parent) {
            return $notification->parent === $parent;
        });
    }

    /** @test */
    public function the_request_can_be_accepted()
    {
        $this->withoutExceptionHandling();
        $parent = User::where('user_type', 'parent')->first();

        $student = User::factory()->create(['default_user_type' => 'student']);

        $this->actingAs($student)
            ->post(
                route('add_as_child_request.accept_request', [
                    'parentId' => $parent->id,
                ]),
            )
            ->assertRedirect();

        $this->assertTrue($student->fresh()->parents->contains($parent));
    }
}
