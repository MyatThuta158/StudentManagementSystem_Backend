<?php
namespace Database\Seeders;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table before seeding to avoid duplicates
        Student::truncate();

        $students = [
            [
                "name"         => "Doris Navarro",
                "email"        => "dorisst@gmail.com",
                "password"     => bcrypt("password"), // Hash passwords
                "phone_number" => "09790000000",
            ],
            [
                "name"         => "Joanne Duke",
                "email"        => "joannest@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000004",
            ],
            [
                "name"         => "Alden Beck",
                "email"        => "dorissta@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000003",
            ],
            [
                "name"         => "Juanita Baird",
                "email"        => "juanitast@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000001",
            ],
            [
                "name"         => "Wallace Cowan",
                "email"        => "wallacest@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000002",
            ],
        ];

        foreach ($students as $studentData) {
            $studentData['created_at'] = Carbon::now('UTC');
            $studentData['updated_at'] = Carbon::now('UTC');

            $student = Student::create($studentData);

            // Assign the "student" role (optional, only if using Spatie roles)
            if (method_exists($student, 'assignRole')) {
                $student->assignRole('student');
            }
        }
    }
}
