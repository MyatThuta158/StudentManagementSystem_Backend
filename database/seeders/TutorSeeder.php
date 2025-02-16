<?php
namespace Database\Seeders;

use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Tutor::truncate();

        $tutors = [
            [
                "name"           => "Doris Navarro1",
                "email"          => "doristt1@gmail.com",
                "password"       => bcrypt("password"),
                "phone_number"   => "09790000000",
                "specialization" => "Mathematics",
            ],
            [
                "name"           => "Joanne Duke1",
                "email"          => "joannett1@gmail.com",
                "password"       => bcrypt("password"),
                "phone_number"   => "09790000004",
                "specialization" => "English",
            ],
            [
                "name"           => "Alden Beck1",
                "email"          => "doristta1@gmail.com",
                "password"       => bcrypt("password"),
                "phone_number"   => "09790000003",
                "specialization" => "English",
            ],
            [
                "name"           => "Juanita Baird1",
                "email"          => "juanitatt1@gmail.com",
                "password"       => bcrypt("password"),
                "phone_number"   => "09790000001",
                "specialization" => "English",
            ],
            [
                "name"           => "Wallace Cowan1",
                "email"          => "wallacett1@gmail.com",
                "password"       => bcrypt("password"),
                "phone_number"   => "09790000002",
                "specialization" => "English",
            ],
        ];

        foreach ($tutors as $tutorData) {

            $tutorData['created_at'] = Carbon::now('UTC');
            $tutorData['updated_at'] = Carbon::now('UTC');

            $tutor = Tutor::create($tutorData);
            // Assign the "tutor" role
            $tutor->assignRole('tutor');
        }
    }
}
