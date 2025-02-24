<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of comments.
     */
    public function index()
    {
        return response()->json(Comments::all(), 200);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:345',
            'blog_id' => 'required|exists:blogs,id',
            'student_id' => 'nullable|exists:students,id',
            'tutor_id' => 'nullable|exists:tutors,id',
        ]);

        $comment = Comments::create($request->all());

        return response()->json([
            'message' => 'Comment added successfully!',
            'comment' => $comment
        ], 201);
    }

    /**
     * Display a specific comment.
     */
    public function show($id)
    {
        $comment = Comments::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json($comment, 200);
    }

    /**
     * Update an existing comment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'sometimes|string|max:345',
        ]);

        $comment = Comments::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->update($request->all());

        return response()->json([
            'message' => 'Comment updated successfully!',
            'comment' => $comment
        ], 200);
    }

    /**
     * Remove a comment from the database.
     */
    public function destroy($id)
    {
        $comment = Comments::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully!'], 200);
    }
}
