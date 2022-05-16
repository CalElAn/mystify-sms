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

        $this->assertEquals('2021/2022, First term (3rd Feb - 3rd Jun)', $term->getFormattedName($academicYear));
    }
}
