<?php
namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ob_clean();
        $user = Auth::user();

        // Check if the authenticated user has permission to create a blog
        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can create blogs.'], 403);
        }

        // Validate only the user-supplied inputs
        try {
            $validatedData = $request->validate([
                'title'        => 'required|string|max:255',
                'content'      => 'required',
                // Validate file if provided
                'DocumentFile' => 'nullable|file|mimes:pdf,doc,docx,txt',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'errors'  => $e->errors(),
                'message' => 'Enter inputs in required fields!',
            ], 422);
        }

        // Determine allocation based on the user's role
        if ($user->hasRole('student')) {
            // Find the allocation where the student is the current user.
            $allocation = Allocation::where('student_id', $user->id)->first();
            // dd($allocation);
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this student'], 404);
            }
            // For a student, the blog gets their own ID as student_id and the allocated tutor as tutor_id.
            $validatedData['student_id']  = $user->id;
            $validatedData['tutor_id']    = $allocation->tutor_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'student';

            //dd($validatedData);
        } elseif ($user->hasRole('tutor')) {
            // Find the allocation where the tutor is the current user.
            $allocation = Allocation::where('tutor_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this tutor'], 404);
            }
            // For a tutor, the blog gets their own ID as tutor_id and the allocated student as student_id.
            $validatedData['tutor_id']    = $user->id;
            $validatedData['student_id']  = $allocation->student_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'tutor';
        } else {
            return response()->json(['error' => 'Only tutors and students can create blogs.'], 403);
        }

        // Handle document upload if a file is provided
        // if ($request->hasFile('DocumentFile')) {
        //     $path                          = $request->file('DocumentFile')->store('documents', 'public');
        //     $validatedData['DocumentFile'] = $path;
        // } else {
        //     $validatedData['DocumentFile'] = null;
        // }

        // Create the blog record
        $blog = Blog::create($validatedData);

        return response()->json(['message' => 'Blog create successfully!'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog = Blog::find($id);

        if (! $blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        return response()->json($blog, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        ob_clean();
        $user = Auth::user();

        // Check if the authenticated user has permission to update the blog
        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can update blogs.'], 403);
        }

        $blog = Blog::find($id);
        if (! $blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        try {
            // Validate only the fields coming from the request
            $validatedData = $request->validate([
                'title'        => 'sometimes|required|string|max:255',
                'content'      => 'sometimes|required',
                // Validate file if provided
                'DocumentFile' => 'nullable|file|mimes:pdf,doc,docx,txt',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Determine allocation and set foreign keys and author details based on user's role
        if ($user->hasRole('student')) {
            // For a student, fetch the allocation where the student is the current user.
            $allocation = Allocation::where('student_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this student.'], 404);
            }
            $validatedData['student_id']  = $user->id;
            $validatedData['tutor_id']    = $allocation->tutor_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'student';
        } elseif ($user->hasRole('tutor')) {
            // For a tutor, fetch the allocation where the tutor is the current user.
            $allocation = Allocation::where('tutor_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this tutor.'], 404);
            }
            $validatedData['tutor_id']    = $user->id;
            $validatedData['student_id']  = $allocation->student_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'tutor';
        } else {
            return response()->json(['error' => 'Only tutors and students can update blogs.'], 403);
        }

        // Handle document update: delete the old file if a new document is provided
        // if ($request->hasFile('DocumentFile')) {
        //     if ($blog->DocumentFile) {
        //         Storage::disk('public')->delete($blog->DocumentFile);
        //     }
        //     $path                          = $request->file('DocumentFile')->store('documents', 'public');
        //     $validatedData['DocumentFile'] = $path;
        // }

        // Update the blog record with the merged data
        $blog->update($validatedData);

        return response()->json(['message' => 'Blog update successfully!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        // Check if the authenticated user has permission to delete the blog
        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can delete blogs.'], 403);
        }

        $blog = Blog::find($id);

        if (! $blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        // Delete the document from storage if it exists
        // if ($blog->DocumentFile) {
        //     Storage::disk('public')->delete($blog->DocumentFile);
        // }

        // Delete the blog record
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully.'], 200);
    }
}
