<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class SchoolModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_school_has_many_users()
    {
        $school = School::factory()->create();
        $user = User::factory(10)->create(['school_id' => $school->school_id]);
     
        $this->assertTrue($school->users->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $school->users[0]);
        $this->assertEquals(10, $school->users->count());
    }

    /** @test */
    public function a_school_can_get_its_students()
    {
        $school = School::factory()->create();
        $user = User::factory(10)->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'student'
        ]);
     
        $this->assertTrue($school->getStudents()->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $school->getStudents()[0]);
        $this->assertEquals(10, $school->getStudents()->count());
    }

    /** @test */
    public function a_school_can_get_its_parents()
    {
        $school = School::factory()->create();

        $student0 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'student'
        ]);
        $student1 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'student'
        ]);
        $student2 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'student'
        ]);
        $parent1 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'parent'
        ]);
        $parent2 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'parent'
        ]);
        $parent3 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'parent'
        ]);
        $parent4 = User::factory()->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'parent'
        ]);

        DB::table('parent_student_pivot')->insert([
            ['student_id' => $student1->id, 'parent_id' => $parent1->id],
            ['student_id' => $student1->id, 'parent_id' => $parent2->id],
            ['student_id' => $student2->id, 'parent_id' => $parent1->id],
            ['student_id' => $student2->id, 'parent_id' => $parent2->id],
            ['student_id' => $student2->id, 'parent_id' => $parent3->id],
        ]);

        $this->assertEquals(3, $school->getParents()->count());
    }

    /** @test */
    public function a_school_can_get_its_teachers()
    {
        $school = School::factory()->create();
        $user = User::factory(10)->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'teacher'
        ]);
        
        $this->assertTrue($school->getTeachers()->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $school->getTeachers()[0]);
        $this->assertEquals(10, $school->getTeachers()->count());
    }

    /** @test */
    public function a_school_can_get_its_administrators()
    {
        $school = School::factory()->create();
        $user = User::factory(10)->create([
            'school_id' => $school->school_id,
            'default_user_type' => 'school administrator'
        ]);
        
        $this->assertTrue($school->getAdministrators()->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $school->getAdministrators()[0]);
        $this->assertEquals(10, $school->getAdministrators()->count());
    }   
}
