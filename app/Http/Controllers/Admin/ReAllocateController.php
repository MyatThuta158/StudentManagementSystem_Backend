<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReallocateRequest;
use App\Mail\ReallocationTutorMail;
use App\Mail\ReallocationMailStudent;
use App\Models\Allocation;
use App\Models\Student;
use App\Models\Tutor;
use App\ResponseModel\ResponseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Log;
use Mail;
use Str;

class ReAllocateController
{

    public function __construct()
    {

    }

    public function reallocate(string $allocateId, ReallocateRequest $request)
    {
        $requested_vars = $request->validated();

        $student = Student::where("id",$requested_vars['student_id'])->first();
        $tutor = Tutor::where("id",$requested_vars['tutor_id'])->first();

        $old_allocate = Allocation::where('id', $allocateId)->first();

        if ($old_allocate == null) {
            return response()->json(ResponseModel::Failed(null, $allocateId, "Old Allocate Id Not Found"));
        }
        if($old_allocate->student_id == $requested_vars['student_id'] && $old_allocate->tutor_id == $requested_vars['tutor_id']){
            return response()->json(ResponseModel::Failed(null, $allocateId, "Cannot reassign the same student and tutor again."));
        }
        $allocate = [
            "name"=> Str::uuid7(),
            "student_id" => $requested_vars['student_id'],
            "tutor_id" => $requested_vars['tutor_id'],
            "staff_id"=>$requested_vars['staff_id'], // this should get from webapplication.
            "section_id" => $old_allocate->section_id,
            "allocation_date"=> $requested_vars['allocation_date'],
            "allocated_by"=>$requested_vars['allocated_by'] // this should get from staff table
        ];
        DB::beginTransaction();
        try {
            $data = Allocation::create($allocate);
            $old_allocate->delete();
            $reallocationTutorMail = new ReallocationTutorMail($student, $tutor);
            $reallocationStudentMail = new ReallocationMailStudent($student, $tutor);
            Mail::to($student->email)->send($reallocationStudentMail);
            Mail::to($tutor->email)->send($reallocationTutorMail);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());
            return response()->json(ResponseModel::Failed(null, $allocateId, "Some Error Happen Please Contact Developer"));
        }
        Log::debug("Reallocate successfully");
        return response()->json(ResponseModel::Ok($data,$data->id , "Reallocate Successfully"));
    }
}