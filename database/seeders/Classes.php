<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classes as ClassModel;

class Classes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassModel::truncate();

        $classlist = [
            [
                "name"=> "Class 1",
                "schedule"=> "9:00 AM - 11:00 AM",
                "room_number"=> "Room 1",
                "created_at"=> Carbon::now(),
                "updated_at"=> Carbon::now(),
            ],
            [
                "name"=> "Class 2",
                "schedule"=> "11:00 AM - 1:00 PM",
                "room_number"=> "Room 2",
                "created_at"=> Carbon::now(),
                "updated_at"=> Carbon::now(),
            ],
            [
                "name"=> "Class 3",
                "schedule"=> "1:00 PM - 3:00 PM",
                "room_number"=> "Room 3",
                "created_at"=> Carbon::now(),
                "updated_at"=> Carbon::now(),
            ],
            [
                "name"=> "Class 4",
                "schedule"=> "3:00 PM - 5:00 PM",
                "room_number"=> "Ball Room",
                "created_at"=> Carbon::now(),
                "updated_at"=> Carbon::now(),
            ],
            [
                "name"=> "Class 5",
                "schedule"=> "5:00 PM - 7:00 PM",
                "room_number"=> "Room 5",
                "created_at"=> Carbon::now(),
                "updated_at"=> Carbon::now(),
            ]
        ];

        ClassModel::insert($classlist);
    }
}
