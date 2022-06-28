<?php

namespace Tests\Feature;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use App\Notifications\AcceptedJoinSchoolRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\JoinSchoolRequest;

class JoinSchoolRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who already has a school cannot view the form
        $this->actingAs(
            User::where('school_id', 1)
                ->get()
                ->random(),
        )
            ->get(route('join_school_request.form'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs(User::factory()->create(['school_id' => null]))
            ->get(route('join_school_request.form'))
            ->assertInertia(
                fn(Assert $page) => $page->component('JoinSchoolRequestForm'),
            );
    }

    /** @test */
    public function the_request_can_be_sent()
    {
        $user = User::factory()->create(['school_id' => null]);

        $this->actingAs($user)
            ->post(
                route('join_school_request.send_request', [
                    'name' => 'random school name which doesnt exist',
                ]),
            )
            ->assertInvalid();

        Notification::fake();

        $schoolToSendNotifTo = School::first();

        $this->actingAs($user)
            ->post(
                route('join_school_request.send_request', [
                    'name' => $schoolToSendNotifTo->name,
                ]),
            )
            ->assertRedirect();

        Notification::assertSentTo($schoolToSendNotifTo, function (
            JoinSchoolRequest $notification,
            $channels,
        ) use ($user) {
            return $notification->user === $user;
        });
    }

    /** @test */
    public function the_request_can_be_accepted()
    {
        $headteacher = User::where('user_type', 'headteacher')->first();

        $userToJoinSchool = User::factory()->create(['school_id' => null]);

        $school = School::first();

        Notification::fake();

        $this->actingAs($headteacher)
            ->post(
                route('join_school_request.accept_request', [
                    'userId' => $userToJoinSchool->id,
                ]),
            )
            ->assertRedirect();

        Notification::assertSentTo($userToJoinSchool, AcceptedJoinSchoolRequest::class);

        $this->assertEquals($userToJoinSchool->fresh()->school, $school);

        $this->actingAs($headteacher)
            ->post(
                route('join_school_request.accept_request', [
                    'userId' => $userToJoinSchool->id,
                ]),
            )
            ->assertRedirect();
    }
}
