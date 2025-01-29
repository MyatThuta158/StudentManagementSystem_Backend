<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;

class Teachers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::truncate();
        Teacher::create([
            "name"=>"Doris Navarro",
            "email"=> "doris@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000000",
            "specialization"=>"Mathematics",
            "created_at"    => Carbon::now(),
            "updated_at"=> Carbon::now(),
        ]);

        Teacher::create([
            "name"=>"Joanne Duke",
            "email"=> "joanne@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000004",
            "specialization"=>"English",
            "created_at"    => Carbon::now(),
            "updated_at"=> Carbon::now(),
        ]);

        Teacher::create([
            "name"=>"Alden Beck",
            "email"=> "doris@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000003",
            "specialization"=>"English",
            "created_at"    => Carbon::now(),
            "updated_at"=> Carbon::now(),
        ]);

        Teacher::create([
            "name"=>"Juanita Baird",
            "email"=> "juanita@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000001",
            "specialization"=>"English",
            "created_at"    => Carbon::now(),
            "updated_at"=> Carbon::now(),
        ]);

        Teacher::create([
            "name"=>"Wallace Cowan",
            "email"=> "wallace@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000002",
            "specialization"=>"English",
            "created_at"    => Carbon::now(),
            "updated_at"=> Carbon::now(),
        ]);
    }
}
