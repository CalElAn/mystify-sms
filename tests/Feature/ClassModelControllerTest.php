<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\ClassTeacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ClassModelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function all_classes_can_be_viewed()
    {
        $academicYearId = 6;

        $parent = User::where('user_type', 'parent')->first();
        //a parent cannot view classes
        $this->actingAs($parent)
            ->get('classes?academicYearId=' . $academicYearId)
            ->assertForbidden();

        $this->actingAs(User::first())
            ->get('classes?academicYearId=' . $academicYearId)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Classes/Index')
                    ->where('classes', function ($classes) use (
                        $academicYearId,
                    ) {
                        $classes->each(function ($class) use ($academicYearId) {
                            $this->assertArrayHasKey('teachers', $class);
                            $this->assertLessThanOrEqual(
                                1,
                                count($class['teachers']),
                            );
                            collect($class['teachers'])?->each(function (
                                $teacher,
                            ) use ($academicYearId) {
                                $this->assertEquals(
                                    $academicYearId,
                                    $teacher['pivot']['academic_year_id'],
                                );
                            });
                        });
                        return true; //this is to make the assertions continue after this function call
                    })
                    ->hasAll(
                        'school',
                        'academicYearsWithTerms',
                        'term',
                    ),
            );
    }

    /** @test */
    public function a_class_can_be_viewed()
    {
        $academicYearId = 6;

        $parent = User::where('user_type', 'parent')->first();
        //a parent cannot view a class
        $this->actingAs($parent)
            ->get('classes/11?academicYearId=' . $academicYearId)
            ->assertForbidden();

        $this->actingAs(User::first())
            ->get('classes/11?academicYearId=' . $academicYearId)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Classes/Show')
                    ->where('classModel', function ($classModel) use (
                        $academicYearId,
                    ) {
                        $this->assertLessThanOrEqual(
                            1,
                            count($classModel['teachers']),
                        );
                        collect($classModel['teachers'])?->each(function (
                            $teacher,
                        ) use ($academicYearId) {
                            $this->assertEquals(
                                $academicYearId,
                                $teacher['pivot']['academic_year_id'],
                            );
                        });
                        collect($classModel['students'])?->each(function (
                            $student,
                        ) use ($academicYearId) {
                            $this->assertEquals(
                                $academicYearId,
                                $student['pivot']['academic_year_id'],
                            );
                        });
                        return true; //this is to make the assertions continue after this function call
                    })
                    ->hasAll(
                        'school',
                        'academicYearsWithTerms',
                        'term',
                    ),
            );
    }

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a headteacher cannot view the form
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->get(route('classes.form'))
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->get(route('classes.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Classes/Form')
                    ->hasAll('classes'),
            );
    }

    /** @test */
    public function a_class_model_can_be_stored()
    {
        $input = [
            'name' => 'Class X',
            'suffix' => 'Y',
        ];

        // a user who is not a headteacher cannot store an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->post(route('classes.store'), $input)
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('classes.store'), ['name' => 'test validation'])
            ->assertInvalid();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('classes.store'), $input)
            ->assertRedirect();

        $this->assertDatabaseHas('classes', $input);
    }

    /** @test */
    public function a_class_model_can_be_updated()
    {
        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $classModel = ClassModel::factory()->create([
            'name' => 'Class W',
            'school_id' => $headteacher->school_id,
        ]);

        $input = [
            'name' => 'Class X',
            'suffix' => 'Y',
        ];

        // a user who is not a headteacher cannot store a class model
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->patch(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
                $input,
            )
            ->assertForbidden();

        // a user who is a headteacher but from another school cannot store an academic year
        $this->actingAs(
            User::factory()->create(['default_user_type' => 'headteacher']),
        )
            ->patch(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
                $input,
            )
            ->assertForbidden();

        $this->actingAs($headteacher)
            ->patch(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
                ['name' => 'test validation'],
            )
            ->assertInvalid();

        $this->actingAs($headteacher)
            ->patch(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
                $input,
            )
            ->assertRedirect();

        $this->assertDatabaseHas('classes', $input);
    }

    /** @test */
    public function a_class_model_can_be_deleted()
    {
        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $classModel = ClassModel::factory()->create([
            'name' => 'to delete',
            'school_id' => $headteacher->school_id,
        ]);

        // a user who is not a headteacher cannot delete an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->delete(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
            )
            ->assertForbidden();

        // a user who is a headteacher but from another school cannot store an academic year
        $this->actingAs(
            User::factory()->create(['default_user_type' => 'headteacher']),
        )
            ->delete(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
            )
            ->assertForbidden();

        $this->actingAs($headteacher)
            ->delete(
                route('classes.update', [
                    'classModel' => $classModel->id,
                ]),
            )
            ->assertRedirect();

        $this->assertDatabaseMissing('classes', ['name' => 'to delete']);
    }
}
