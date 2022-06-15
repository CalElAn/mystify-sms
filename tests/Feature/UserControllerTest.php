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

    protected $seed = true;

    /** @test */
    public function the_users_index_can_be_viewed()
    {
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

    /** @test */
    public function a_user_can_change_his_user_type()
    {
        //a parent cannot change his user type
        $this->actingAs(
            User::where('default_user_type', 'parent')->first(),
        )
            ->patch('/users/change-user-type')
            ->assertForbidden();

        //a student cannot change his user type
        $this->actingAs(
            User::where('default_user_type', 'student')->first(),
        )
            ->patch('/users/change-user-type')
            ->assertForbidden();

        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $this->actingAs($headteacher)
            ->patch('/users/change-user-type', ['user_type' => 'parent'])
            ->assertRedirect();

        $this->assertEquals($headteacher->fresh()->user_type, 'parent');
    }
}
