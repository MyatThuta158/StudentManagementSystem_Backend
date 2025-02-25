<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // ✅ Add this

class AllocationController extends Controller
{
    /**
     * Store a new allocation
     */
    public function store(Request $request)
    {
        Log::info("🔥 Creating Allocation", ['request' => $request->all()]);

        $request->validate([
            'name' => 'required|string|max:255',
            'allocation_date' => 'required|date',
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $staff = Auth::user();

        if (!$staff) {
            Log::error("❌ Unauthorized access attempt to create allocation");
            return response()->json(['error' => 'Unauthorized. Please login as staff.'], 401);
        }

        $allocation = Allocation::create([
            'name' => $request->name,
            'allocation_date' => $request->allocation_date,
            'allocated_by' => $staff->name,
            'staff_id' => $staff->id,
            'tutor_id' => $request->tutor_id,
            'student_id' => $request->student_id,
        ]);

        Log::info("✅ Allocation Created Successfully", ['allocation' => $allocation]);

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
        try {
            Log::info("🔍 Search Request Received", ['params' => $request->all()]);

            $query = Allocation::query();

            if ($request->has('name')) {
                Log::info("🔎 Filtering by name", ['name' => $request->name]);
                $query->whereRaw("name ILIKE ?", ["%" . $request->name . "%"]);
            }

            // ✅ Fetch data and force return before empty check
            $allocations = $query->with(['staff', 'tutor', 'student'])->get();

            // **Debugging: Return the raw SQL query and results**
            return response()->json([
                'debug' => [
                    'request' => $request->all(),
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                    'allocations' => $allocations // ✅ Show actual data
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("❌ Error in search", ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong!',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
