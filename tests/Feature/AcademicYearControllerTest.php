<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class AcademicYearControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a headteacher cannot view the form
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->get(route('academic_years.form'))
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->get(route('academic_years.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('AcademicYears/Form')
                    ->hasAll('academicYears'),
            );
    }

    /** @test */
    public function an_academic_year_can_be_stored()
    {
        $input = [
            'name' => '2019/2020',
            'start_date' => '2019-1-3',
            'end_date' => '2020-6-3',
        ];

        // a user who is not a headteacher cannot store an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->post(route('academic_years.store'), $input)
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('academic_years.store'), ['name' => 'test validation'])
            ->assertInvalid();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('academic_years.store'), $input)
            ->assertRedirect();

        $this->assertDatabaseHas('academic_years', $input);
    }

    /** @test */
    public function an_academic_year_can_be_updated()
    {
        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $academicYear = AcademicYear::factory()->create([
            'school_id' => $headteacher->school_id,
        ]);

        $input = [
            'name' => 'changed name',
            'start_date' => '2019-1-3',
            'end_date' => '2020-6-3',
        ];

        // a user who is not a headteacher cannot store an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->patch(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
                $input,
            )
            ->assertForbidden();

        // a user who is a headteacher but from another school cannot store an academic year
        $this->actingAs(
            User::factory()->create(['default_user_type' => 'headteacher']),
        )
            ->patch(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
                $input,
            )
            ->assertForbidden();

        $this->actingAs($headteacher)
            ->patch(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
                ['name' => 'test validation'],
            )
            ->assertInvalid();

        $this->actingAs($headteacher)
            ->patch(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
                $input,
            )
            ->assertRedirect();

        $this->assertDatabaseHas('academic_years', $input);
    }

    /** @test */
    public function an_academic_year_can_be_deleted()
    {
        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $academicYear = AcademicYear::factory()->create([
            'name' => 'to delete',
            'school_id' => $headteacher->school_id,
        ]);

        // a user who is not a headteacher cannot delete an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->delete(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
            )
            ->assertForbidden();

        // a user who is a headteacher but from another school cannot store an academic year
        $this->actingAs(
            User::factory()->create(['default_user_type' => 'headteacher']),
        )
            ->delete(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
            )
            ->assertForbidden();

        $this->actingAs($headteacher)
            ->delete(
                route('academic_years.update', [
                    'academicYear' => $academicYear->id,
                ]),
            )
            ->assertRedirect();

        $this->assertDatabaseMissing('academic_years', ['name' => 'to delete']);
    }
}
