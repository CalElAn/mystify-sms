<?php

namespace Database\Seeders;

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
        //seed school table and terms table after
        foreach (User::all() as $user) {
            if ($user->default_user_type === 'student') {
                DB::table('school_fees')->insert([
                    'student_id' => $user->id,
                    'school_id' => $user->school_id,
                    'term_id' => 1,
                    'amount' => rand(500, 600),
                ]);
            }
        }

        $faker = \Faker\Factory::create();

        foreach (User::all() as $user) {
            if ($user->default_user_type === 'student') {
                DB::table('school_fees_paid')->insert([
                    [
                        'student_id' => $user->id,
                        'school_id' => $user->school_id,
                        'term_id' => 1,
                        'amount' => rand(0, 100),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ],
                    [
                        'student_id' => $user->id,
                        'school_id' => $user->school_id,
                        'term_id' => 1,
                        'amount' => rand(0, 200),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ],
                    [
                        'student_id' => $user->id,
                        'school_id' => $user->school_id,
                        'term_id' => 1,
                        'amount' => rand(0, 600),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ],
                ]);
            }
        }
        // \App\Models\User::factory(10)->create();
    }
}
