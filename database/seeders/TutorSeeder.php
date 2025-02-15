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
        Tutor::create([
            "name"=>"Doris Navarro",
            "email"=> "doristt@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000000",
            "specialization"=>"Mathematics",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Tutor::create([
            "name"=>"Joanne Duke",
            "email"=> "joannett@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000004",
            "specialization"=>"English",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Tutor::create([
            "name"=>"Alden Beck",
            "email"=> "doristt@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000003",
            "specialization"=>"English",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Tutor::create([
            "name"=>"Juanita Baird",
            "email"=> "juanitatt@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000001",
            "specialization"=>"English",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Tutor::create([
            "name"=>"Wallace Cowan",
            "email"=> "wallacett@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000002",
            "specialization"=>"English",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);
    }
}
