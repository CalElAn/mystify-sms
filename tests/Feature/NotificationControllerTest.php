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

    /** @test */
    public function the_notification_index_can_be_viewed()
    {
        $this->seed();

        $this->actingAs(User::factory()->create())
            ->get(route('notifications.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Notifications/Index')
                    ->hasAll('notifications'),
            );
    }
}
