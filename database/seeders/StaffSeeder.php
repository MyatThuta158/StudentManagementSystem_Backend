<?php

namespace Database\Seeders;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Staff::truncate();
        Staff::create([
            "name"=>"Doris Navarro",
            "email"=> "doris@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000000",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Staff::create([
            "name"=>"Joanne Duke",
            "email"=> "joanne@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000004",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Staff::create([
            "name"=>"Alden Beck",
            "email"=> "doris@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000003",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Staff::create([
            "name"=>"Juanita Baird",
            "email"=> "juanita@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000001",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);

        Staff::create([
            "name"=>"Wallace Cowan",
            "email"=> "wallace@gmail.com",
            "password"=> bcrypt("password"),
            "phone_number"=>"09790000002",
            "created_at"    => Carbon::now('UTC'),
            "updated_at"=> Carbon::now('UTC'),
        ]);
    }
}
