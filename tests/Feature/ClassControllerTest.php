<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ClassTeacherPivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ClassControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_classes_can_be_viewed()
    {
        $this->seed();

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
                                $this->assertArrayHasKey(
                                    'unique_subjects',
                                    $teacher,
                                );
                            });
                        });
                        return true; //this is to make the assertions continue after this function call
                    })
                    ->hasAll(
                        'school',
                        'academicYearsWithTerms',
                        'showTerm',
                        'term',
                    ),
            );
    }

    /** @test */
    public function a_class_can_be_viewed()
    {
        $this->seed();

        $academicYearId = 6;

        $parent = User::where('user_type', 'parent')->first();
        //a parent cannot view a class
        $this->actingAs($parent)
            ->get('class/11?academicYearId=' . $academicYearId)
            ->assertForbidden();

        $this->actingAs(User::first())
            ->get('class/11?academicYearId=' . $academicYearId)
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
                            $this->assertArrayHasKey(
                                'unique_subjects',
                                $teacher,
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
                        'showTerm',
                        'term',
                    ),
            );
    }
}
