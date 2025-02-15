<?php

namespace Database\Seeders;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::truncate();
        Student::create([
            "name"=>"Doris Navarro",
            "email"=> "dorisst@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000000",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Student::create([
            "name"=>"Joanne Duke",
            "email"=> "joannest@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000004",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Student::create([
            "name"=>"Alden Beck",
            "email"=> "dorisst@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000003",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Student::create([
            "name"=>"Juanita Baird",
            "email"=> "juanitast@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000001",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Student::create([
            "name"=>"Wallace Cowan",
            "email"=> "wallacest@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000002",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);
    }
}
