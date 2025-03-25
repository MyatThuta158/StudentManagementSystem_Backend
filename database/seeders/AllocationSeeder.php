<?php
namespace Database\Seeders;

use App\Models\Allocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Allocation::truncate();
        Allocation::create([
            "student_id"      => 2,
            "tutor_id"        => 1,
            "staff_id"        => 1,
            "allocated_by"    => "Kyawe",
            "allocation_date" => Carbon::now('UTC')->toDateString(),
        ]);
    }
}
