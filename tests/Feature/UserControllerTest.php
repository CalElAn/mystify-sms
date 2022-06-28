<?php

namespace Tests\Feature;

use App\Models\ParentStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

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
            'default_user_type' => 'student',
            'school_id' => 1,
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
                        ->hasAll('school', 'userType', 'nameFilter'),
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
        $this->actingAs(User::where('default_user_type', 'parent')->first())
            ->patch(route('change_user_type'))
            ->assertForbidden();

        //a student cannot change his user type
        $this->actingAs(User::where('default_user_type', 'student')->first())
            ->patch(route('change_user_type'))
            ->assertForbidden();

        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $this->actingAs($headteacher)
            ->patch(route('change_user_type', ['user_type' => 'parent']))
            ->assertRedirect();

        $this->assertEquals($headteacher->fresh()->user_type, 'parent');
    }

    /** @test */
    public function the_remove_children_form_can_be_viewed()
    {
        //a user who is not a parent cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'parent')->first())
            ->get(route('remove_children.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'parent')->first())
            ->get(route('remove_children.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('RemoveChildrenForm')
                    ->hasAll('children'),
            );
    }

    /** @test */
    public function the_show_user_page_can_be_viewed()
    {
        $user = User::all()->random();

        $this->actingAs($user)
            ->get(route('users.show', ['user' => $user->id]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Users/Show')
                    ->hasAll('user'),
            );
    }

    /** @test */
    public function a_user_can_be_updated()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user = User::factory()->create();

        $imageUploadResponse = $this->post('/filepond/process', [
            'filepond' => UploadedFile::fake()->image('testImage1.jpg'),
        ]);
        $fileAtFilepondTempLocation = $imageUploadResponse->content();

        $input = [
            'name' => 'changed name',
            'email' => 'email@changed.com',
            'phone_number' => 'changed phone_number',
            'filepond' => $fileAtFilepondTempLocation,
        ];

        $this->actingAs(User::factory()->create())
            ->patch("/users/{$user->id}", $input)
            ->assertForbidden();

        $response = $this->actingAs($user)->patch("/users/{$user->id}", $input);

        $response->assertRedirect();

        $pathToSaveProfilePictureAt = 'profile_pictures/' . $user->id;

        /** @var \Illuminate\Filesystem\Filesystem */
        $storagePublicDisk = Storage::disk('public');
        $storagePublicDisk->assertMissing(
            'filepond/tmp/' . $fileAtFilepondTempLocation,
        );
        $storagePublicDisk->assertExists($pathToSaveProfilePictureAt);

        $this->assertDatabaseHas('users', [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone_number' => $input['phone_number'],
            'profile_picture_path' => $pathToSaveProfilePictureAt,
        ]);
    }

    /** @test */
    public function a_parent_can_delete_a_child()
    {
        $parentStudentModel = ParentStudent::factory()->create();

        $this->actingAs(User::where('user_type', 'parent')->first())
            ->delete(
                route('parent_student.destroy', [
                    'parentStudent' => $parentStudentModel->id,
                ]),
            )
            ->assertRedirect();

        $this->assertDatabaseMissing(
            'parent_student',
            $parentStudentModel->getAttributes(),
        );
    }

    /** @test */
    public function the_change_password_form_can_be_viewed()
    {
        $user = User::all()->random();

        $this->actingAs($user)
            ->get(route('change_password_form'))
            ->assertInertia(
                fn(Assert $page) => $page->component(
                    'Users/ChangePasswordForm',
                ),
            );
    }

    /** @test */
    public function a_user_can_change_his_password()
    {
        $user = User::all()->random();

        $this->actingAs($user)
            ->patch(
                route('change_password', [
                    'current_password' => 'password',
                    'password' => 'password1',
                    'password_confirmation' => 'password1',
                ]),
            );
        
        $this->assertTrue(Hash::check('password1', $user->fresh()->password));
    }
}
