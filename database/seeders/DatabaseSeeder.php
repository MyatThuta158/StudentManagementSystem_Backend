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
            
            $this->call(Teachers::class);
            $this->call(Classes::class);
            $this->call(Courses::class);
            
            \DB::commit();
        }catch(\Exception $e){
            \DB::rollBack();
            \Log::error($e->getMessage());
        }
    }
}
