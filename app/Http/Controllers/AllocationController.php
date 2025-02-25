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
        Log::info(" Creating Allocation", ['request' => $request->all()]);

        $request->validate([
            'allocation_date' => 'required|date',
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $staff = Auth::user();

        if (!$staff) {
            Log::error(" Unauthorized access attempt to create allocation");
            return response()->json(['error' => 'Unauthorized. Please login as staff.'], 401);
        }

        $allocation = Allocation::create([
            'allocation_date' => $request->allocation_date,
            'allocated_by' => $staff->name,
            'staff_id' => $staff->id,
            'tutor_id' => $request->tutor_id,
            'student_id' => $request->student_id,
        ]);

        Log::info(" Allocation Created Successfully", ['allocation' => $allocation]);

        return response()->json([
            'message' => 'Allocation created successfully!',
            'allocation' => $allocation
        ], 201);
    }

    /**
     * Get all allocations with details
     */
    public function index()
    {
        $allocations = Allocation::with(['staff', 'tutor', 'student'])->get();

        return response()->json(['allocations' => $allocations], 200);
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
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $search = $request->input('query');

        // Log search input
        Log::info("Search Query: " . $search);

        // Search for allocations linked to tutors or students
        $allocations = Allocation::with(['staff', 'tutor', 'student'])
            ->whereHas('tutor', function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%"); // Use ILIKE for PostgreSQL (Case-Insensitive)
            })
            ->orWhereHas('student', function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%");
            })
            ->get();

        // Log results
        Log::info("Search Results: ", $allocations->toArray());

        if ($allocations->isEmpty()) {
            return response()->json(['message' => 'Allocation not found'], 404);
        }

        return response()->json(['allocations' => $allocations], 200);
    }
}
