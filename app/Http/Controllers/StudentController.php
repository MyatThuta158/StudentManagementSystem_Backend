<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    // This show all student lists
    public function index()
    {
        $students = Student::whereNotIn('id', function ($query) {
            $query->select('student_id')->from('allocations');
        })->paginate(10);

        // dd($students);
        return response()->json($students);
    }

    public function search(Request $request)
    {

        $searchTerm = $request->input('query');

        $students = Student::where('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
            ->get();

        return response()->json($students);
    }

    //----------this is to make ascending and descending order based on student name---//
    public function sortStudents(Request $request)
    {
        // Get the sort direction from the request (default to 'asc')
        $sortDirection = $request->input('sort', 'asc');

        // Validate the sort direction to ensure it is either 'asc' or 'desc'
        if (! in_array($sortDirection, ['asc', 'desc'])) {
            return response()->json(['error' => 'Invalid sort direction. Use "asc" or "desc".'], 400);
        }

        // Fetch students sorted by name in the specified direction
        $students = Student::orderBy('name', $sortDirection)->get();

        return response()->json($students);
    }

    //----------this is to make ascending and descending order based on student id---//
    public function sortId(Request $request)
    {
        // Get the sort direction from the request (default to 'asc')
        $sortDirection = $request->input('sort', 'asc');

        // Validate the sort direction to ensure it is either 'asc' or 'desc'
        if (! in_array($sortDirection, ['asc', 'desc'])) {
            return response()->json(['error' => 'Invalid sort direction. Use "asc" or "desc".'], 400);
        }

        // Fetch students sorted by name in the specified direction
        $students = Student::orderBy('StudentID', $sortDirection)->get();

        return response()->json($students);
    }
}
