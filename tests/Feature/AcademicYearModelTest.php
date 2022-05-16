<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AcademicYearModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_academic_year_has_many_terms()
    {
        $academicYear = AcademicYear::factory()->create();
        Term::factory()->create(['academic_year_id' => $academicYear->academic_year_id]);

        $this->assertInstanceOf('App\Models\Term', $academicYear->terms->first());
    }
}
