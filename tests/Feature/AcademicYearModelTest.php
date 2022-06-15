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
        Term::factory()->create(['academic_year_id' => $academicYear->id]);

        $this->assertInstanceOf('App\Models\Term', $academicYear->terms->first());
    }

    /** @test */
    public function an_academic_year_has_a_formatted_name()
    {
        $academicYear = AcademicYear::factory()->create([
            'name' => '2021/2022',
            'start_date' => '2021-2-3',
            'end_date' => '2022-6-3'
        ]);

        $this->assertEquals(
            '2021/2022 (3rd Feb - 3rd Jun)',
            $academicYear->formatted_name,
        );
    }
}
