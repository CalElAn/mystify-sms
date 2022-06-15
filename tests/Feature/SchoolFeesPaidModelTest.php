<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\SchoolFeesPaid;

class SchoolFeesPaidModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_school_fees_paid_model_belongs_to_a_student()
    {
        $schoolFeesPaid = SchoolFeesPaid::factory()->create();

        $this->assertInstanceOf('App\Models\User', $schoolFeesPaid->student);
    }
}
