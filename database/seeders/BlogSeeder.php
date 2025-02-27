<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('blogs')->insert([
            [
                'title' => 'Introduction to Laravel',
                'content' => 'This blog post covers the basics of Laravel framework.',
                'author' => 'John Doe',
                'student_id' => 1, // Assign to a student (or set to null)
                'tutor_id' => null, // Since a student wrote it
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Best Practices in PHP',
                'content' => 'This blog post covers PHP coding standards and best practices.',
                'author' => 'Jane Smith',
                'student_id' => null, // Since a tutor wrote it
                'tutor_id' => 1, // Assign to a tutor (or set to null)
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Building Secure APIs',
                'content' => 'This blog post explains how to secure Laravel APIs using authentication and authorization.',
                'author' => 'Alice Johnson',
                'student_id' => null, // Since a tutor wrote it
                'tutor_id' => 2, // Assign to another tutor
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
