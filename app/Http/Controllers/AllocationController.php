<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AllocationController extends Controller
{
    /**
     * Store a new allocation
     */
    public function store(Request $request)
    {
        Log::info("Creating Allocation", ['request' => $request->all()]);

        $request->validate([
            'allocation_date' => 'required|date',
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $staff = Auth::user();

        if (!$staff) {
            Log::error("Unauthorized access attempt to create allocation");
            return response()->json(['error' => 'Unauthorized. Please login as staff.'], 401);
        }


        $existingAllocation = Allocation::where('student_id', $request->student_id)->first();
        if ($existingAllocation) {
            return response()->json(['error' => 'This student is already allocated to a tutor.'], 409);
        }


        $allocation = Allocation::create([
            'allocation_date' => $request->allocation_date,
            'allocated_by' => $staff->name,
            'staff_id' => $staff->id,
            'tutor_id' => $request->tutor_id,
            'student_id' => $request->student_id,
        ]);

        Log::info("Allocation Created Successfully", ['allocation' => $allocation]);

        return response()->json([
            'message' => 'Allocation created successfully!',
            'allocation' => $allocation
        ], 201);
    }


    /**
     * Get all allocations with details
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default 10 per page
        $allocations = Allocation::with(['staff', 'tutor', 'student'])->orderByDesc('created_at')->paginate($perPage);

        return response()->json($allocations, 200);
    }


    /**
     * Get a single allocation with details
     */
    public function show($id)
    {
        // Ensure ID is treated as an integer
        $id = (int) $id;

        $allocation = Allocation::with(['staff', 'tutor', 'student'])->find($id);

        if (!$allocation) {
            return response()->json(['message' => 'Allocation not found'], 404);
        }

        return response()->json(['allocation' => $allocation], 200);
    }
    public function search(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default 10 per page
        $query = Allocation::with(['staff', 'tutor', 'student']);

        if ($request->has('tutor_name')) {
            $query->whereHas('tutor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->tutor_name . '%');
            });
        }

        if ($request->has('student_name')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->student_name . '%');
            });
        }

        $allocations = $query->paginate($perPage); // Apply pagination

        return response()->json($allocations, 200);
    }
}
