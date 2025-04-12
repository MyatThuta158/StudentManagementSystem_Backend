<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Student;
use App\Models\Tutor;
use Carbon\Carbon;

class AdminReportController extends Controller
{
public function AdminReport()
    {
        // Retrieve all students that have at least one allocation.
        // We eager-load the tutor through the allocation, blogs, and comments.
        $blogs = $this->getAverageMessageToStudents();
        $students = Student::whereHas('allocations')
            ->with(['allocations.tutor', 'blogs', 'comments'])
            ->get();

        //    dd($students);

        /**
         * Group 1: Students with allocation made but no login.
         * - Criteria: last_login_at is null.
         * - Inactive days are computed from the allocation date.
         */
        $groupNoLogin = $students->filter(function ($student) {
            // Only consider students that have never logged in.
            if ($student->last_login_at !== null) {
                return false;
            }
            // Use the allocation date from the first allocation record.
            $allocation = $student->allocations->first();
            if (! $allocation || ! $allocation->allocation_date) {
                return false;
            }
            $allocationDate = Carbon::parse($allocation->allocation_date);
            $inactiveDays   = $allocationDate->diffInDays(Carbon::now());
            return $inactiveDays > 7;
        })->map(function ($student) {
            $allocation     = $student->allocations->first();
            $allocationDate = Carbon::parse($allocation->allocation_date);
            $inactiveDays   = $allocationDate->diffInDays(Carbon::now());
            $tutorName      = isset($allocation->tutor->name) ? $allocation->tutor->name : null;

            return [
                'student_code'  => $student->StudentID,
                'email'         => $student->email,
                'last_login'    => "No login",
                'inactive_days' => $inactiveDays,
                'tutor_name'    => $tutorName,
            ];
        })->values(); // Re-index the collection

        //  dd($groupNoLogin);

        /**
         * Group 2: Students with allocation made and login recorded.
         * Inactive days are computed as follows:
         * - If the student has created any blog or comment records, use the later of the blog or comment's created date.
         * - Otherwise, use the allocation date.
         */
        $groupLoginCalculated = [];

        foreach ($students as $student) {
            // Only consider students who have logged in.
            if (is_null($student->last_login_at)) {
                continue;
            }

            // Retrieve the first allocation.
            $allocation = $student->allocations->first();

            //  dd($allocation->allocation_date);
            if (! $allocation || ! $allocation->allocation_date) {
                continue;
            }

            // Parse the allocation date.
            $allocationDate = Carbon::parse($allocation->allocation_date);

            // Determine the reference date for calculating inactivity.
            // If the student has not created any blogs or comments, use the allocation date.
            if ($student->blogs->isEmpty() && $student->comments->isEmpty()) {
                $lastActivity = $allocationDate;
            } else {
                // If blogs or comments exist, find the most recent created_at date.
                $latestBlog = $student->blogs->isNotEmpty()
                ? Carbon::parse($student->blogs->max('created_at'))
                : null;

                // dd($latestBlog);
                $latestComment = $student->comments->isNotEmpty()
                ? Carbon::parse($student->comments->max('created_at'))
                : null;

                if ($latestBlog && $latestComment) {
                    // Choose the later of the two.
                    $lastActivity = $latestBlog->greaterThan($latestComment)
                    ? $latestBlog
                    : $latestComment;
                } else {
                    $lastActivity = $latestBlog ?? $latestComment;
                }
            }

            // Calculate the inactive days from the chosen date.
            $inactiveDays = $lastActivity->diffInDays(Carbon::now());

            // dd($inactiveDays);
            // Only include the student if inactive days exceed 7.
            if ($inactiveDays > 7) {
                $groupLoginCalculated[] = [
                    'student_code'  => $student->StudentID,
                    'email'         => $student->email,
                    'last_login'    => Carbon::parse($student->last_login_at)->format('Y-m-d'),
                    'inactive_days' => $inactiveDays,
                    'tutor_name'    => isset($allocation->tutor->name) ? $allocation->tutor->name : null,
                ];
            }
        }

        // For debugging: Uncomment the following line to inspect the groupLoginCalculated data.
        //dd($groupLoginCalculated);

        $studentsWithoutTutor = Student::whereDoesntHave('allocations', function ($query) {
            $query->whereNotNull('tutor_id');
        })->get();

        // Return both groups in a JSON response.
        return response()->json([
            'Average_Interaction' => $blogs,
            'group_no_login'                => $groupNoLogin,
            'group_login_calculated'        => $groupLoginCalculated,
            'student_without_personalTutor' => $studentsWithoutTutor,
        ]);
    }

    private function getAverageMessageToStudents(){
        $blogs = Blog::where("author_role",'tutor')->with("comments")->get();
        $blogs = $blogs->groupBy(["author","author_role"]);
        $blogs = $blogs->map(function($e){
            $totalInteractions = 0;
            $tutor = Tutor::where("name",$e->first()->first()->author)->first();
            $e->map(function($s) use ($totalInteractions,$tutor){
                $s->map(function($k) use ($totalInteractions,$tutor){
                    $totalInteractions += $k->comments->where("tutor_id",$tutor->id)->count();
                });
            });
            $mth = ceil(Carbon::parse($tutor->created_at,"UTC")->diffInMonths(Carbon::now('UTC')));
            return ($e->count() + $totalInteractions) / $mth;
        });
        return $blogs;
    }
}
