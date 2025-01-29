<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class Courses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::truncate();
        Course::create([
            "name"=>"Myanmar",
            "description"=>"Myanmar Language",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);

        Course::create([
            "name"=>"English",
            "description"=>"IELTS English Language",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);

        Course::create([
            "name"=>"Mathematics",
            "description"=>"derivative and integral",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);

        Course::create([
            "name"=>"Mathematics",
            "description"=>"Geometric Mathematics",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);

        Course::create([
            "name"=>"Science",
            "description"=>"Science for heat and light",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);

        Course::create([
            "name"=>"Physics",
            "description"=>"Physics for heat , light and speed",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);

        Course::create([
            "name"=>"Chemistry",
            "description"=>"Chemistry for chemical reaction",
            "credits"=> 4,
            "start_date"=> new \DateTime("now"),
            "end_date"=> new \DateTime("now"),
            "min_mark"=>40
        ]);
    }
}
