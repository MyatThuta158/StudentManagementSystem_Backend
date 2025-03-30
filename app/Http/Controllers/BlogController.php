<?php
namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Blog;
use App\Models\BlogDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optionally, fetch blogs with their documents:
        // $blogs = Blog::with('documents')->get();
        // return response()->json($blogs, 200);
    }

    /**
     * Store a newly created blog and its documents in storage.
     */
    public function store(Request $request)
    {
        // Clear any previous output.
        ob_clean();
        $user = Auth::user();

        // Only tutors and students can create blogs.
        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can create blogs.'], 403);
        }

        try {
            // Validate blog data and an optional array of document files.
            $validatedData = $request->validate([
                'title'       => 'required|string|max:255',
                'content'     => 'required',
                'documents'   => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,txt',
            ]);

            //  dd($validatedData['documents']);
        } catch (ValidationException $e) {
            return response()->json([
                'errors'  => $e->errors(),
                'message' => 'Enter inputs in required fields!',
            ], 422);
        }

        // Determine allocation and set foreign keys/author details based on the user's role.
        if ($user->hasRole('student')) {
            $allocation = Allocation::where('student_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this student'], 404);
            }
            $validatedData['student_id']  = $user->id;
            $validatedData['tutor_id']    = $allocation->tutor_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'student';
        } elseif ($user->hasRole('tutor')) {
            $allocation = Allocation::where('tutor_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this tutor'], 404);
            }
            $validatedData['tutor_id']    = $user->id;
            $validatedData['student_id']  = $allocation->student_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'tutor';
        } else {
            return response()->json(['error' => 'Only tutors and students can create blogs.'], 403);
        }

        // Use a DB transaction to ensure all-or-nothing saving.
        DB::beginTransaction();
        try {
            // Create the blog record.
            $blog = Blog::create($validatedData);

            // Handle multiple document uploads if provided.
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    // Store the document file using Laravel's storage.
                    $path = $document->store('documents', 'public');
                    // Create a BlogDocument record linking the file to the blog.
                    BlogDocument::create([
                        'blog_id'          => $blog->id,
                        'BlogDocumentFile' => $path,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error saving blog: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Blog and documents saved successfully!'], 201);
    }

    /**
     * Display the specified blog along with its documents.
     */
    public function show(string $id)
    {
        $blog = Blog::with('documents')->find($id);

        if (! $blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        return response()->json($blog, 200);
    }

    /**
     * Update the specified blog and add new documents if provided.
     */
    public function update(Request $request, string $id)
    {
        ob_clean();
        $user = Auth::user();

        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can update blogs.'], 403);
        }

        $blog = Blog::find($id);
        if (! $blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        try {
            // Validate incoming blog fields and an optional array of new document files.
            $validatedData = $request->validate([
                'title'       => 'sometimes|required|string|max:255',
                'content'     => 'sometimes|required',
                'documents.*' => 'nullable|file|mimes:pdf,doc,docx,txt',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Set allocation and author details based on the user's role.
        if ($user->hasRole('student')) {
            $allocation = Allocation::where('student_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this student.'], 404);
            }
            $validatedData['student_id']  = $user->id;
            $validatedData['tutor_id']    = $allocation->tutor_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'student';
        } elseif ($user->hasRole('tutor')) {
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

        // Update the blog record with the validated data.
        $blog->update($validatedData);

        // If new documents are provided, add them without removing the existing ones.
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('blog_documents', 'public');
                BlogDocument::create([
                    'blog_id'          => $blog->id,
                    'BlogDocumentFile' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Blog updated successfully!'], 200);
    }

    /**
     * Remove the specified blog and its documents from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can delete blogs.'], 403);
        }

        $blog = Blog::with('documents')->find($id);
        if (! $blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        // Delete each associated document file and its record.
        foreach ($blog->documents as $document) {
            if ($document->BlogDocumentFile) {
                Storage::disk('public')->delete($document->BlogDocumentFile);
            }
            $document->delete();
        }

        // Finally, delete the blog.
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully.'], 200);
    }
}
