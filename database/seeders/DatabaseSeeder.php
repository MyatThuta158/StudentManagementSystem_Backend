<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try{
            \DB::beginTransaction();
            
            $this->call(TutorSeeder::class);
            $this->call(StudentSeeder::class);
            $this->call(StaffSeeder::class);
            $this->call(SectionSeeder::class);
            $this->call(AllocationSeeder::class);
            
            \DB::commit();
        }catch(\Exception $e){
            \DB::rollBack();
            \Log::error($e->getMessage());
        }
    }
}
