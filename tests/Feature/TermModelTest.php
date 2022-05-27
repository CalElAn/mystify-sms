<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_term_has_a_formatted_name()
    {
        $academicYear = AcademicYear::factory()->create();
        $term = Term::factory()->create(['academic_year_id' => $academicYear->academic_year_id]);

        $this->assertEquals('2021/2022, First term (3rd Feb - 3rd Jun)', $term->formatted_name);
    }

    /** @test */
    public function a_term_has_a_formatted_short_name()
    {
        $academicYear = AcademicYear::factory()->create();
        $term = Term::factory()->create(['academic_year_id' => $academicYear->academic_year_id]);

        $this->assertEquals('2021/2022, First term', $term->formatted_short_name);
    }

    /** @test */
    public function a_term_has_one_academic_year()
    {
        $academicYear = AcademicYear::factory()->create();
        $term = Term::factory()->create(['academic_year_id' => $academicYear->academic_year_id]);

        $this->assertInstanceOf('App\Models\AcademicYear', $term->academicYear);
    }

}
