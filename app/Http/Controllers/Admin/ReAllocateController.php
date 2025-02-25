<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReallocateRequest;
use App\Mail\ReallocationTutorMail;
use App\Mail\ReallocationMailStudent;
use App\Models\Allocation;
use App\Models\Arranging;
use App\Models\Blog;
use App\Models\Student;
use App\Models\Tutor;
use App\ResponseModel\ResponseModel;
use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReAllocateController
{

    public function __construct()
    {

    }

    public function reallocate(string $allocateId, ReallocateRequest $request)
    {
        $staff_id = auth()->user()->id;
        $staff_name = auth()->user()->name;
        if (auth()->user()->hasAnyRole('staff')) {
            $requested_vars = $request->safe();

            $student = Student::where("id", $requested_vars['student_id'])->first();
            $tutor = Tutor::where("id", $requested_vars['tutor_id'])->first();

            $old_allocate = Allocation::where('id', $allocateId)->first();

            if ($old_allocate == null) {
                return response()->json(ResponseModel::Failed(null, $allocateId, "Old Allocate Id Not Found"));
            }
            if ($old_allocate->student_id == $requested_vars['student_id'] && $old_allocate->tutor_id == $requested_vars['tutor_id']) {
                return response()->json(ResponseModel::Failed(null, $allocateId, "Cannot reassign the same student and tutor again."));
            }
            $allocate = [
                "name" => Str::uuid7(),
                "student_id" => $requested_vars['student_id'],
                "tutor_id" => $requested_vars['tutor_id'],
                "staff_id" => $staff_id, // this should get from webapplication.
                "allocation_date" => $requested_vars['allocation_date'],
                "allocated_by" => $staff_name // this should get from staff table
            ];
            DB::beginTransaction();
            try {
                // get required data and update
                $data = Allocation::create($allocate);
                $old_blogs = Blog::where('tutor_id', $old_allocate->tutor_id)->where('student_id', $old_allocate->student_id)->get();
                $old_blogs = $old_blogs->map(function ($i, $index) use ($requested_vars) {
                    $i->tutor_id = $requested_vars['tutor_id'];
                })->toArray();
                $arranging_list = Arranging::where('tutor_id', $old_allocate->tutor_id)->where('student_id', $old_allocate->student_id)->get();
                $arranging_list = $arranging_list->map(function ($i, $index) use ($requested_vars) {
                    $i->tutor_id = $requested_vars['tutor_id'];
                })->toArray();
                //end 
                // region update database region
                $old_allocate->delete();
                Blog::update($old_blogs);
                Arranging::update($arranging_list);
                $reallocationTutorMail = new ReallocationTutorMail($student, $tutor);
                $reallocationStudentMail = new ReallocationMailStudent($student, $tutor);
                // endregion
                Mail::to($student->email)->send($reallocationStudentMail);
                Mail::to($tutor->email)->send($reallocationTutorMail);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::debug($e->getMessage());
                return response()->json(ResponseModel::Failed(null, $allocateId, "Some Error Happen Please Contact Developer"));
            }
            Log::debug("Reallocate successfully");
            return response()->json(ResponseModel::Ok($data, $data->id, "Reallocate Successfully"));
        }
        return response()->json(ResponseModel::Failed(null, "", "Unauthorized to access"), 403);
    }
}