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

        return response()->json(["data" => $arrangings ], 200);
    }


    public function store(Request $request)
    {
        // 🔍 Ensure Sanctum authentication is working
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        // ✅ Ensure the user is a student by checking the model
        if (!$user instanceof \App\Models\Student) {
            return response()->json(['message' => 'Only students can create an arrangement.'], 403);
        }

        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'status' => 'required|in:pending,completed,canceled',
        ]);

        $existingArrangement = Arranging::where('student_id', $user->id)
            ->where('tutor_id', $validated['tutor_id'])
            ->whereIn('status', ['pending', 'completed'])
            ->exists();

        if ($existingArrangement) {
            return response()->json(['message' => 'You already have an active arrangement with this tutor.'], 409);
        }

        $arranging = Arranging::create([
            'student_id' => $user->id,
            'tutor_id' => $validated['tutor_id'],
            'status' => $validated['status'],
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
