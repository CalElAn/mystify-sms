<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_users_index_can_be_viewed()
    {
        $this->seed();

        $parentUser = User::factory()->create([
            'user_type' => 'parent',
        ]);
        $studentUser = User::factory()->create([
            'user_type' => 'student',
        ]);

        $this->actingAs($parentUser)
            ->get('users/students')
            ->assertForbidden();

        $this->actingAs($parentUser)
            ->get('users/parents')
            ->assertForbidden();
        $this->actingAs($studentUser)
            ->get('users/parents')
            ->assertForbidden();

        $this->actingAs($parentUser)
            ->get('users/teachers')
            ->assertForbidden();

        $this->actingAs($parentUser)
            ->get('users/administrators')
            ->assertForbidden();

        $this->actingAs(
            User::factory()->create([
                'user_type' => 'parent',
            ]),
        )
            ->get('users/students')
            ->assertForbidden();

        $user = User::where('user_type', 'headteacher')->first();


        $assertProps = function ($userType) use ($user) {

            $this->actingAs($user)
                ->get("users/{$userType}?nameFilter=an")
                ->assertInertia(
                    fn(Assert $page) => $page
                        ->component('Users/Index')
                        ->where('users', function ($users) {
                            collect($users['data'])->each(
                                fn(
                                    $user,
                                ) => $this->assertStringContainsStringIgnoringCase(
                                    'an',
                                    $user['name'],
                                ),
                            );
                            return true;
                        })
                        ->hasAll(
                            'school',
                            'showTerm',
                            // 'users',
                            'userType',
                            'nameFilter',
                        ),
                );
        };

        foreach (
            ['students', 'parents', 'teachers', 'administrators']
            as $value
        ) {
            $assertProps($value);
        }
    }
}
