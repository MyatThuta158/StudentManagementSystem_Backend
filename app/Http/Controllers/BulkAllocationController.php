<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAllocationRequest;
use App\Models\Allocation;
use App\Models\Student;
use App\Models\Tutor;
use App\Notifications\AllocatedStudent;
use App\Notifications\AllocatedTutor;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use DB;
use Log;

class BulkAllocationController extends Controller
{
    public function allocate(BulkAllocationRequest $request)
    {
        $requested_vars = $request->safe();

        $allocates = [];
        foreach ($requested_vars->student_ids as $i) {
            $assigned_student = Allocation::where('student_id', $i)->exists();
            if ($assigned_student) {
                return response()->json(ResponseModel::Failed(false, "", "There's contains already assigned student."));
            }
            $tmpAllocate = [
                'allocation_date' => $requested_vars['allocation_date'] ?? Carbon::now('utc'),
                'allocated_by' => auth()->user()->name,
                'staff_id' => auth()->user()->id,
                'tutor_id' => $requested_vars['tutor_id'],
                'student_id' => $i
            ];
            array_push($allocates, $tmpAllocate);
        }
        DB::beginTransaction();
        try {
            foreach ($allocates as $i) {
                Allocation::create($i);
                // send notification
                $student = Student::where("id", $i['student_id'])->first();
                $tutor = Tutor::where("id", $i['tutor_id'])->first();

                $student->notify(new AllocatedStudent($student,$tutor));
                $tutor->notify(new AllocatedTutor($student,$tutor));
                // end send notification
            }
            Log::info("Bulk Imported Successfully");
            DB::commit();
            return response()->json(ResponseModel::Ok(true, "", "Successfully allocated students with tutor."));
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(ResponseModel::Failed(false, "", "Some error found please contact developer."));
        }
    }
    public function nonAllocatedStudentList()
    {
        if (auth()->user()->hasAnyRole(['staff', 'tutor'])) {
            $allocated_student_ids = Student::with('allocations')->get();
            $allocated_student_ids = $allocated_student_ids->filter(function ($i, $index) {
                if ($i->allocations->isEmpty()) {
                    return $i;
                }
            })->values();
            return response()->json(ResponseModel::Ok($allocated_student_ids, "", "Successfully fetched the unallocated students."));
        }
    }
}

