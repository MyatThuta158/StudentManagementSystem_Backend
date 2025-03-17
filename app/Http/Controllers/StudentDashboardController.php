<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Document;
use App\Models\MeetingDetail;
use Illuminate\Support\Facades\DB;

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

        // Retrieve vlogs uploaded by the tutor
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

        // ---------------- Document Statistics ---------------- //
        // Total documents created by the authenticated user in the "document" table.
        $totalDocuments = Document::where('created_by', $student->id)
            ->where('created_type', 'student')
            ->count();

        // Fetch the count of documents per status from the "arrangement_document" table.
        $documentStatusesQuery = DB::table('arrangement_document')
            ->select('status', DB::raw('count(*) as total'))
            ->where('created_by', $student->id)
            ->groupBy('status')
            ->get();

        // Convert the result to an associative array for easy consumption.
        $documentStatuses = [];
        foreach ($documentStatusesQuery as $status) {
            $documentStatuses[$status->status] = $status->total;
        }

        //------------Fetch related document for table----//
        $documents = Document::select('document.file_name', 'arrangement_document.status', 'document.created_at')
            ->join('arrangement_document', 'document.id', '=', 'arrangement_document.document_id')
            ->where('document.created_by', $student->id)
            ->get();
        // ------------------------------------------------------- //

        // return the JSON response.
        return response()->json([
            'status' => 200,
            'data'   => [
                'user'           => [
                    'name'          => $userInfo['name'],
                    'email'         => $userInfo['email'],
                    'last_login_at' => $userInfo['last_login_at'],
                ],
                'vlogs'          => [
                    'student'   => $studentVlogs,
                    'tutor'     => $tutorVlogs,
                    'totalBlog' => $totalBlog,
                ],
                'meetings'       => [
                    'count_online' => $onlineMeetingsCount,
                    'count_campus' => $campusMeetingsCount,
                    'totalMeeting' => $totalMeeting,
                ],
                'documentsTotal' => [
                    'total'  => $totalDocuments,
                    'status' => $documentStatuses,
                ],

                'documents'      => $documents,
            ],
        ]);
    }
}
