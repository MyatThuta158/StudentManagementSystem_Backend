<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\MeetingDetail;

class StudentDashboardController extends Controller
{
    /**
     * Return all dashboard info in a single API call.
     */
    public function getDashboardData()
    {
        // Get the currently authenticated student.
        $student = auth()->user();

        // Prepare the user information.
        $userInfo = [
            'name'          => $student->name,
            'email'         => $student->email,
            'last_login_at' => $student->last_login_at,
        ];

        // Retrieve vlogs uploaded by the student (author_role = "student")
        $studentVlogs = Blog::where('student_id', $student->id)
            ->where('author_role', 'student')
            ->count();

        // Retrieve vlogs uploaded by the tutor (author_role = "tutor")
        $tutorVlogs = Blog::where('student_id', $student->id)
            ->where('author_role', 'tutor')
            ->count();

        $totalBlog = $studentVlogs + $tutorVlogs;

        // Count online meetings for this student.
        $onlineMeetingsCount = MeetingDetail::with('arranging')
            ->where('meeting_type', 'online')
            ->whereHas('arranging', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->count();

        // Count campus meetings for this student.
        $campusMeetingsCount = MeetingDetail::with('arranging')
            ->where('meeting_type', 'campus') // Use 'offline' if that's your enum value.
            ->whereHas('arranging', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->count();

        $totalMeeting = $onlineMeetingsCount + $campusMeetingsCount;

        // Build and return the JSON response.
        return response()->json([
            'status' => 200,
            'data'   => ['user' => [
                'name'          => $userInfo['name'],
                'email'         => $userInfo['email'],
                'last_login_at' => $userInfo['last_login_at'],
            ],
                'vlogs'             => [
                    'student'   => $studentVlogs,
                    'tutor'     => $tutorVlogs,
                    'totalBlog' => $totalBlog,
                ],
                'meetings'          => [
                    'count_online' => $onlineMeetingsCount,
                    'count_campus' => $campusMeetingsCount,
                    'totalMeeting' => $totalMeeting,
                ]],
        ]);
    }
}
