<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassTeacher;
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
        $gradingScaleId = GradingScale::factory()->create()->id;

        //*create school
        $school = School::factory()->create([
            'grading_scale_id' => $gradingScaleId,
        ]);

        //*create users
        User::factory()->create([
            'email' => 'ce@example.com',
            'default_user_type' => 'headteacher',
            'user_type' => 'headteacher',
            'school_id' => $school->id,
        ]);
        $teacher = User::factory()->create([
            'email' => 'te@example.com',
            'default_user_type' => 'teacher',
            'user_type' => 'teacher',
            'school_id' => $school->id,
        ]);
        User::factory()->create([
            'email' => 'se@example.com',
            'default_user_type' => 'student',
            'user_type' => 'student',
            'school_id' => $school->id,
        ]);
        User::factory(100)->create(['school_id' => $school->id]);
        $allUsers = User::all();
        $allStudents = $allUsers->where('default_user_type', 'student');
        $allTeachers = $allUsers
            ->where('default_user_type', 'teacher')
            ->values();
        $allParents = $allUsers->where('default_user_type', 'parent')->values();

        //*select two random parents for each student
        $parentRecordsToInsert = [];
        foreach ($allStudents as $studentToBeGivenParents) {
            $randomParents = $allParents->random(2)->values();
            $parentRecordsToInsert[] = [
                'student_id' => $studentToBeGivenParents->id,
                'parent_id' => $randomParents[0]->id,
            ];
            $parentRecordsToInsert[] = [
                'student_id' => $studentToBeGivenParents->id,
                'parent_id' => $randomParents[1]->id,
            ];
        }
        DB::table('parent_student')->insert($parentRecordsToInsert);

        //*create an array that maps each student id to a random class suffix
        $studentsAndClassSuffixesArray = [];
        foreach ($allStudents as $allStudentsItem) {
            $studentsAndClassSuffixesArray[
                $allStudentsItem->id
            ] = $faker->randomElement(['A', 'B']);
        }

        //*create an array that maps each student id to a random class suffix
        $teachersAndClassSuffixesArray = [];
        foreach ($allTeachers as $allTeachersItem) {
            $teachersAndClassSuffixesArray[
                $allTeachersItem->id
            ] = $faker->randomElement(['A', 'B']);
        }

        //* to create subjects
        $subjectsArray = [
            'English',
            'Mathematics',
            'Science',
            'French',
            'Religious and Moral Education',
            'Social Studies',
            'Basic Design in Technology',
            'Ghanaian Language',
            'ICT',
        ];
        foreach ($subjectsArray as $item) {
            DB::table('subjects')->insert([
                'name' => $item,
            ]);
        }

        for ($i = 1; $i <= 6; $i++) {
            //*create class 1A and B to 6A and B,
            $className = 'Class ' . $i;
            $classIds = [];
            $classIds['A'] = DB::table('classes')->insertGetId([
                'school_id' => $school->id,
                'name' => $className,
                'suffix' => 'A',
            ]);
            $classIds['B'] = DB::table('classes')->insertGetId([
                'school_id' => $school->id,
                'name' => $className,
                'suffix' => 'B',
            ]);

            //*create academic year 2001 to 2006
            $academicYearName = 2000 + $i;
            $academicYear = AcademicYear::factory()->create([
                'id' => $i,
                'school_id' => $school->id,
                'name' => $academicYearName,
                'start_date' => "{$academicYearName}-2-3",
                'end_date' => "{$academicYearName}-11-3",
            ]);

            //*create first and second terms for each academic year
            $firstTerm = Term::factory()->create([
                'academic_year_id' => $academicYear->id,
                'name' => 'first term',
                'start_date' => "{$academicYearName}-2-3",
                'end_date' => "{$academicYearName}-6-10",
            ]);
            $secondTerm = Term::factory()->create([
                'academic_year_id' => $academicYear->id,
                'name' => 'second term',
                'start_date' => "{$academicYearName}-8-3",
                'end_date' => "{$academicYearName}-11-3",
            ]);

            //*for each academic year, insert a class teacher for Class A and B
            //TODO there should be a class_teacher entry for all years for all classes
            ClassTeacher::factory()->create([
                'teacher_id' => $allTeachers[0]->id,
                'class_id' => $classIds['A'],
                'academic_year_id' => $academicYear->id,
            ]);
            ClassTeacher::factory()->create([
                'teacher_id' => $allTeachers[1]->id,
                'class_id' => $classIds['B'],
                'academic_year_id' => $academicYear->id,
            ]);

            $schoolFeesRecordsToInsert = [];
            $schoolFeesPaidRecordsToInsert = [];
            $classStudentRecordsToInsert = [];
            $gradesRecordsToInsert = [];

            foreach ($allStudents as $student) {
                //*to create school fees
                $schoolFeesRecordsToInsert[] = [
                    'student_id' => $student->id,
                    'school_id' => $student->school_id,
                    'academic_year_id' => $academicYear->id,
                    'amount' => rand(500, 600),
                ];

                //*to create school fees paid (3 for each student)
                for ($j = 0; $j < 3; $j++) {
                    $schoolFeesPaidRecordsToInsert[] = [
                        'student_id' => $student->id,
                        'school_id' => $student->school_id,
                        'academic_year_id' => $academicYear->id,
                        'amount' => rand(0, 100),
                        'created_at' => $faker->dateTimeBetween(
                            '-3 months',
                            'now',
                        ),
                    ];
                }

                //*for each academic year and class, insert a "class_student" record for each student
                //*for example: in 2001 put all students in either Class 1A or B, in 2002 put all students in either Class 2A or B, and so on
                $classStudentRecordsToInsert[] = [
                    'class_id' =>
                        $classIds[$studentsAndClassSuffixesArray[$student->id]],
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ];

                //* for each class (1 to 6), for each student, for each term (1 and 2), for each subject, create grades
                foreach ([$firstTerm, $secondTerm] as $termItem) {
                    foreach ($subjectsArray as $subject) {
                        $gradesRecordsToInsert[] = [
                            'school_id' => $student->school_id,
                            'student_id' => $student->id,
                            'teacher_id' => $teacher->id,
                            'term_id' => $termItem->id,
                            'class_name' => 'Class ' . $i,
                            'class_suffix' =>
                                $studentsAndClassSuffixesArray[$student->id],
                            'subject_name' => $subject,
                            'class_mark' => random_int(0, 30),
                            'exam_mark' => random_int(0, 70),
                        ];
                    }
                }
            }
            DB::table('school_fees')->insert($schoolFeesRecordsToInsert);
            DB::table('school_fees_paid')->insert(
                $schoolFeesPaidRecordsToInsert,
            );
            DB::table('class_student')->insert(
                $classStudentRecordsToInsert,
            );
            DB::table('grades')->insert($gradesRecordsToInsert);

            //*create notice board items for each term 1 and 2
            foreach ([$firstTerm, $secondTerm] as $termItem) {
                NoticeBoard::factory(25)->create([
                    'school_id' => $school->id,
                    'term_id' => $termItem->id,
                    'user_id' => 1, //the headteacher
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                ]);
            }
        }
    }
}
