<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $user = Auth::user();
        // dd($user->hasRole('manager'));

        // Check if the authenticated user is a 'manager'
        if (! $user || ! $user->can('manage blog ')) {
            return response()->json(['error' => 'Only tutors and students can register new admins.'], 403);
        }

        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'author'     => 'required|string|max:255',
                'student_id' => 'required|exists:students,id',
                'tutor_id'   => 'required|exists:tutors,id',
                'section_id' => 'required|exists:sections,id',
                'body'       => 'required',
                'header'     => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            // Return JSON response with validation errors and 422 status code
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }

        // Create the blog record using the validated data
        $blog = Blog::create($validatedData);

        // Return a JSON response with the created blog and HTTP status 201
        return response()->json($blog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
