<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    // This show all student lists
    public function index()
    {

        $students = Student::paginate(10); //Return 10 students in each

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
}
