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

        // ---------------- VLOG STATISTICS ---------------- //
        // Retrieve vlogs uploaded by the student (author_role = "student")
        $studentVlogs = Blog::where('student_id', $student->id)
            ->where('author_role', 'student')
            ->count();

        // Retrieve vlogs uploaded by the tutor (author_role = "tutor")
        $tutorVlogs = Blog::where('student_id', $student->id)
            ->where('author_role', 'tutor')
            ->count();

        $totalBlog = $studentVlogs + $tutorVlogs;

        // ---------------- MEETING STATISTICS ---------------- //
        // Count online meetings for this student.
        $onlineMeetingsCount = MeetingDetail::with('arranging')
            ->where('meeting_type', 'online')
            ->whereHas('arranging', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->count();

        // Count campus meetings for this student.
        $campusMeetingsCount = MeetingDetail::with('arranging')
            ->where('meeting_type', 'campus')
            ->whereHas('arranging', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->count();

        $totalMeeting = $onlineMeetingsCount + $campusMeetingsCount;

        // ---------------- DOCUMENT STATISTICS ---------------- //
        // Total documents created by the authenticated user in the "document" table.
        $totalDocuments = Document::where('created_by', $student->id)
            ->where('created_type', 'student')
            ->count();

        // Fetch the count of documents per status from the joined tables.
        // This uses the new pivot (arrangement_document) and assignment_arrangements table.
        $documentStatusesQuery = DB::table('arrangement_document')
            ->join('assignment_arrangements', 'arrangement_document.assignment_arrangement_id', '=', 'assignment_arrangements.id')
            ->join('document', 'arrangement_document.document_id', '=', 'document.id')
            ->select('assignment_arrangements.status', DB::raw('count(*) as total'))
            ->where('document.created_by', $student->id)
            ->where('document.created_type', 'student')
            ->groupBy('assignment_arrangements.status')
            ->get();

        $documentStatuses = [];
        foreach ($documentStatusesQuery as $row) {
            $documentStatuses[$row->status] = $row->total;
        }

        // Fetch detailed document information, including:
        // - file_name (from document)
        // - uploaded_date (from document.created_at)
        // - deadline (from assignment_arrangements.dead_line)
        // - status (from assignment_arrangements.status)
        $documents = Document::select(
            'document.file_name',
            'document.created_at as uploaded_date',
            'assignment_arrangements.dead_line as deadline',
            'assignment_arrangements.status'
        )
            ->join('arrangement_document', 'document.id', '=', 'arrangement_document.document_id')
            ->join('assignment_arrangements', 'arrangement_document.assignment_arrangement_id', '=', 'assignment_arrangements.id')
            ->where('document.created_by', $student->id)
            ->where('document.created_type', 'student')
            ->get();

        // ---------------- TUTOR ALLOCATION INFO ---------------- //
        // Retrieve the student's allocation with the related tutor.
        $allocation = $student->allocations()->with('tutor')->first();
        $tutorName  = $allocation && $allocation->tutor ? $allocation->tutor->name : null;
        $tutorEmail = $allocation && $allocation->tutor ? $allocation->tutor->email : null;

        // ---------------- RETURN RESPONSE ---------------- //
        return response()->json([
            'status' => 200,
            'data'   => [
                'user'           => [
                    'name'          => $userInfo['name'],
                    'email'         => $userInfo['email'],
                    'last_login_at' => $userInfo['last_login_at'],
                ],
                'tutor'          => [
                    'name'  => $tutorName,
                    'email' => $tutorEmail,
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
