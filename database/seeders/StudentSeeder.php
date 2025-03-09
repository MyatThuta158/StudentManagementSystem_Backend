<?php
namespace Database\Seeders;

use App\Models\Student;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table before seeding to avoid duplicates
        Student::truncate();

        $faker = Faker::create();

        for ($i = 1; $i <= 100; $i++) {
            $studentData = [
                "StudentID"    => sprintf("STD%03d", $i), 
                "name"         => $faker->name,
                "email"        => $faker->unique()->safeEmail,
                "password"     => bcrypt("password"),              
                "phone_number" => $faker->numerify('097900#####'), 
                "created_at"   => Carbon::now('UTC'),
                "updated_at"   => Carbon::now('UTC'),
            ];

            $student = Student::create($studentData);

            // Optionally assign the "student" role if using Spatie roles
            if (method_exists($student, 'assignRole')) {
                $student->assignRole('student');
            }
        }
    }
}
