<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_notification_index_can_be_viewed()
    {
        $this->actingAs(User::factory()->create())
            ->get(route('notifications.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Notifications/Index')
                    ->hasAll('notifications'),
            );
    }

    /** @test */
    public function the_school_notification_index_can_be_viewed()
    {
        $this->actingAs(User::where('default_user_type', 'headteacher')->first())
            ->get(route('notifications.view_school_notifications'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Notifications/SchoolNotifications')
                    ->hasAll('notifications'),
            );
    }
}
