<?php

namespace Database\Seeders;

use App\Models\Allocation;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Allocation::create([
            "student_id"=>1,
            "tutor_id"=>1,
            "section_id"=>1,
            "staff_id"=>1,
            "name"=> "Hello World",
            "allocated_by"=> "Kyawe",
            "allocation_date"=>Carbon::now('UTC')->toDateString()
        ]);
    }
}
