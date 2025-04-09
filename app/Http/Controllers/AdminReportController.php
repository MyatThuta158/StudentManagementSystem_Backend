<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Tutor;

class AdminReportController extends Controller
{
    public function AdminReport()
    {
        $tutors = Tutor::withCount(['blogs', 'comments'])->get();

        dd($tutors);
        $totalTutors = $tutors->count();

        // Sum total blogs and comments across all tutors.
        $totalBlogs    = $tutors->sum('blogs_count');
        $totalComments = $tutors->sum('comments_count');

        // Calculate the overall average (the average total count per tutor).
        $totalAverage = $totalTutors > 0 ? ($totalBlogs + $totalComments) / $totalTutors : 0;

        // 2. Retrieve students without a personal tutor allocation.
        // This uses whereDoesntHave with a condition to ignore allocations with a non-null tutor_id.
        $studentsWithoutTutor = Student::whereDoesntHave('allocations', function ($query) {
            $query->whereNotNull('tutor_id');
        })->get();

        // 3. Return the JSON response with total average, tutor details, and student records.
        return response()->json([
            'total_average'          => $totalAverage,
            'tutors'                 => $tutors,
            'students_without_tutor' => $studentsWithoutTutor,
        ]);
    }
}
