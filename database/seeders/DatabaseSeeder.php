<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\GradingScale;
use App\Models\NoticeBoard;
use App\Models\School;
use App\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        //*create default grading scale
        $gradingScaleId = GradingScale::factory()->create()->grading_scale_id;

        //*create school
        $school = School::factory()->create(['grading_scale_id' => $gradingScaleId]);
        
        //*create users
        User::factory()->create([
            'email' => 'ce@example.com', 
            'default_user_type' => 'headteacher', 
            'user_type' => 'headteacher', 
            'school_id' => $school->school_id
        ]);
        $teacher = User::factory()->create([
            'default_user_type' => 'teacher', 
            'user_type' => 'teacher', 
            'school_id' => $school->school_id
        ]);
        User::factory(100)->create(['school_id' => $school->school_id]);
        $allUsers = User::all();
        $allStudents = $allUsers->where('default_user_type', 'student');

        //*create an array that maps each student id to a class suffix
        $studentsAndClassSuffixesArray = [];
        foreach ($allStudents as $student) {
            $studentsAndClassSuffixesArray[$student->id] = $faker->randomElement(['A', 'B']);           
        }

        //* to create subjects
        $subjectsArray = [
            'English', 'Mathematics', 'Science', 'French', 'Religious and Moral Education', 'Social Studies',
            'Basic Design in Technology', 'Ghanaian Language', 'ICT',
        ];
        foreach ($subjectsArray as $item) {
            DB::table('subjects')->insert([
                'name' => $item,
            ]);
        }

        for ($i=1; $i<=6; $i++) { 
            //*create class 1A and B to 6A and B, 
            $className = 'Class '.$i;
            $classIds = [];
            $classIds['A'] = DB::table('classes')->insertGetId([
                'school_id' => $school->school_id,
                'name' => $className,
                'suffix' => 'A',
            ]);
            $classIds['B'] = DB::table('classes')->insertGetId([
                'school_id' => $school->school_id,
                'name' => $className,
                'suffix' => 'B',
            ]);

            //*create academic year 2001 to 2006
            $academicYearName = 2000 + $i;
            $academicYear = AcademicYear::factory()->create([
                'academic_year_id' => $i,
                'school_id' => $school->school_id,
                'name' => $academicYearName,
                'start_date' => "{$academicYearName}-2-3",
                'end_date' => "{$academicYearName}-11-3"
            ]);

            //*create first and second terms for each academic year
            $firstTerm = Term::factory()->create([
                'academic_year_id' => $academicYear->academic_year_id,
                'name' => 'first term',
            ]);
            $secondTerm = Term::factory()->create([
                'academic_year_id' => $academicYear->academic_year_id,
                'name' => 'second term',
            ]);

            foreach ($allStudents as $student) {
                //*to create school fees
                DB::table('school_fees')->insert([
                    'student_id' => $student->id,
                    'school_id' => $student->school_id,
                    'academic_year_id' => $academicYear->academic_year_id,
                    'amount' => rand(500, 600),
                ]);

                //*to create school fees paid
                DB::table('school_fees_paid')->insert([
                    [
                        'student_id' => $student->id,
                        'school_id' => $student->school_id,
                        'academic_year_id' => $academicYear->academic_year_id,
                        'amount' => rand(0, 100),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ],
                    [
                        'student_id' => $student->id,
                        'school_id' => $student->school_id,
                        'academic_year_id' => $academicYear->academic_year_id,
                        'amount' => rand(0, 200),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ],
                    [
                        'student_id' => $student->id,
                        'school_id' => $student->school_id,
                        'academic_year_id' => $academicYear->academic_year_id,
                        'amount' => rand(0, 600),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ],
                ]);

                //*for each academic year and class, insert a "class_student_pivot" record for each student
                //*for example: in 2001 put all students in either Class 1A or B, in 2002 put all students in either Class 2A or B, and so on
                DB::table('class_student_pivot')->insert([
                    'class_id' => $classIds[$studentsAndClassSuffixesArray[$student->id]],
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->academic_year_id,
                ]);

                //*to create grades for each student for each class (1 to 6) for term 1 and 2
                foreach ([$firstTerm, $secondTerm] as $termItem) {
                    foreach ($subjectsArray as $subject) {
                        DB::table('grades')->insert([
                            'school_id' => $student->school_id,
                            'student_id' => $student->id,
                            'teacher_id' => $teacher->id, 
                            'term_id' => $termItem->term_id,
                            'class_name' => 'Class '.$i,
                            'class_suffix' => $studentsAndClassSuffixesArray[$student->id],
                            'subject_name' => $subject,
                            'class_mark' => random_int(0, 30),
                            'exam_mark' => random_int(0, 70),
                        ]); 
                    }                  

                }
            }

            //*create notice board items for each term 1 and 2
            foreach([$firstTerm, $secondTerm] as $termItem) {
                NoticeBoard::factory(25)->create([
                    'school_id' => $school->school_id,
                    'term_id' => $termItem->term_id,
                    'user_id' => 1, //the headteacher
                    'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                ]);
            }
        }
    }
}
