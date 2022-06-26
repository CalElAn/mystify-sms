<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Term;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Testing\Fluent\AssertableJson;

class TermControllerTest extends TestCase
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
            ->get(route('terms.form'))
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->get(route('terms.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Terms/Form')
                    ->hasAll('academicYears'),
            );
    }

    /** @test */
    public function terms_can_be_gotten_from_an_academic_year()
    {
        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->getJson(route('academic_years.terms', ['academicYear' => 6]))
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has('terms', 2)
                    ->has(
                        'terms.0',
                        fn($json) => $json->where('academic_year_id', 6)->etc(),
                    )
                    ->has(
                        'terms.1',
                        fn($json) => $json->where('academic_year_id', 6)->etc(),
                    ),
            );
    }

    /** @test */
    public function a_term_can_be_stored()
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
            ->post(
                route('academic_years.terms.store', ['academicYear' => 6]),
                $input,
            )
            ->assertForbidden();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(route('academic_years.terms.store', ['academicYear' => 6]), [
                'name' => 'test validation',
            ])
            ->assertInvalid();

        $this->actingAs(
            User::where('default_user_type', 'headteacher')->first(),
        )
            ->post(
                route('academic_years.terms.store', ['academicYear' => 6]),
                $input,
            )
            ->assertRedirect();

        $this->assertDatabaseHas('terms', $input);
    }

    /** @test */
    public function a_term_can_be_updated()
    {
        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $term = Term::factory()->create(['academic_year_id' => 6]);

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
                route('terms.update', [
                    'term' => $term->id,
                ]),
                $input,
            )
            ->assertForbidden();

        // a user who is a headteacher but from another school cannot store an academic year
        $this->actingAs(
            User::factory()->create(['default_user_type' => 'headteacher']),
        )
            ->patch(
                route('terms.update', [
                    'term' => $term->id,
                ]),
                $input,
            )
            ->assertForbidden();

        $this->actingAs($headteacher)
            ->patch(
                route('terms.update', [
                    'term' => $term->id,
                ]),
                ['name' => 'test validation'],
            )
            ->assertInvalid();

        $this->actingAs($headteacher)
            ->patch(
                route('terms.update', [
                    'term' => $term->id,
                ]),
                $input,
            )
            ->assertRedirect();

        $this->assertDatabaseHas('terms', $input);
    }

    /** @test */
    public function a_term_can_be_deleted()
    {
        $headteacher = User::where('default_user_type', 'headteacher')->first();

        $term = Term::factory()->create([
            'name' => 'to delete',
            'academic_year_id' => 6,
        ]);

        // a user who is not a headteacher cannot delete an academic year
        $this->actingAs(
            User::where('default_user_type', '<>', 'headteacher')->first(),
        )
            ->delete(
                route('terms.destroy', [
                    'term' => $term->id,
                ]),
            )
            ->assertForbidden();

        // a user who is a headteacher but from another school cannot delete a term
        $this->actingAs(
            User::factory()->create(['default_user_type' => 'headteacher']),
        )
            ->delete(
                route('terms.destroy', [
                    'term' => $term->id,
                ]),
            )
            ->assertForbidden();

        $this->actingAs($headteacher)
            ->delete(
                route('terms.destroy', [
                    'term' => $term->id,
                ]),
            )
            ->assertRedirect();

        $this->assertDatabaseMissing('terms', ['name' => 'to delete']);
    }
}
