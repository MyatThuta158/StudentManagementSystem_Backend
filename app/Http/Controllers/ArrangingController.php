<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arranging;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ArrangingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default to 10 per page, customizable
        $arrangings = Arranging::with(['student', 'tutor'])->paginate($perPage);

        return response()->json(["data" => $arrangings], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        //  Ensure the user is a tutor
        if (!$user instanceof \App\Models\Tutor) {
            return response()->json(['message' => 'Only tutors can create an arrangement.'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        //  Check if the tutor already has an active arrangement with this student
        $existingArrangement = Arranging::where('tutor_id', $user->id)
            ->where('student_id', $validated['student_id'])
            ->whereIn('status', ['pending', 'completed'])
            ->exists();

        if ($existingArrangement) {
            return response()->json(['message' => 'You already have an active arrangement with this student.'], 409);
        }

        //  Automatically set the status to "pending"
        $arranging = Arranging::create([
            'tutor_id' => $user->id,
            'student_id' => $validated['student_id'],
            'status' => 'pending', // Auto-set status
        ]);

        return response()->json([
            'message' => 'Arrangement created successfully!',
            'arranging' => $arranging
        ], 201);
    }


    public function show($id)
    {
        $arranging = Arranging::with(['student', 'tutor'])->find($id);

        if (!$arranging) {
            return response()->json(['message' => 'Arrangement not found'], 404);
        }

        return response()->json($arranging, 200);
    }

    public function update(Request $request, $id)
    {
        $arranging = Arranging::find($id);

        if (!$arranging) {
            return response()->json(['message' => 'Arrangement not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,completed,canceled',
        ]);

        $arranging->update($validated);

        return response()->json(['message' => 'Arrangement updated successfully!', 'arranging' => $arranging], 200);
    }

    public function destroy($id)
    {
        $arranging = Arranging::find($id);

        if (!$arranging) {
            return response()->json(['message' => 'Arrangement not found'], 404);
        }

        $arranging->delete();

        return response()->json(['message' => 'Arrangement deleted successfully'], 200);
    }
}
