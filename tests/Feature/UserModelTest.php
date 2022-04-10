<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_belongs_to_a_school()
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->school_id]);

        $this->assertInstanceOf('App\Models\School', $user->school);
        $this->assertEquals($school->fresh(), $user->school);
    }

    /** @test */
    public function a_user_who_is_a_student_belongs_to_many_parents()
    {
        $student = User::factory()->create(['default_user_type' => 'student']);
        $parent1 = User::factory()->create(['default_user_type' => 'parent']);
        $parent2 = User::factory()->create(['default_user_type' => 'parent']);

        DB::table('parent_student_pivot')->insert([
            ['student_id' => $student->id, 'parent_id' => $parent1->id],
            ['student_id' => $student->id, 'parent_id' => $parent2->id],
        ]);

        $this->expectException('LogicException');
        $parent1->parents;

        $this->assertTrue($student->parents->contains($parent1));
        $this->assertTrue($student->parents->contains($parent2));
        $this->assertInstanceOf('App\Models\User', $student->parents[0]);
    }
}
