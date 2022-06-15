<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\SchoolFees;

class SchoolFeesModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_school_fees_model_belongs_to_a_student()
    {
        $schoolFees = SchoolFees::factory()->create();

        $this->assertInstanceOf('App\Models\User', $schoolFees->student);
    }
}
