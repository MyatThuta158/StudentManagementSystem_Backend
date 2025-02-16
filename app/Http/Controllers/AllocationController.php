<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'allocation_date' => 'required|date',
            'allocated_by' => 'required|string|max:255',
            'staff_id' => 'required|exists:staffs,id',
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $allocation = Allocation::create($request->all());

        return response()->json(['message' => 'Allocation created successfully', 'allocation' => $allocation], 201);
    }
    public function index()
    {
        $allocations = Allocation::all(); // Fetch all allocations

        return response()->json(['allocations' => $allocations], 200);
    }
    public function show($id)
    {
        $allocation = Allocation::with(['student','section', 'section.blogs' , 'section.blogs.comments' ,'tutor'])->find($id); // Find allocation by ID
        if (!$allocation) {
            return response()->json(['message' => 'Allocation not found'], 404);
        }

        return response()->json(['allocation' => $allocation], 200);
    }
}
