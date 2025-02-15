<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReallocateRequest;
use App\Models\Allocation;
use App\ResponseModel\ResponseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Log;
use Str;

class ReAllocateController
{

    public function __construct()
    {

    }

    public function reallocate(string $allocateId, ReallocateRequest $request)
    {
        $requested_vars = $request->validated();

        $old_allocate = Allocation::where('id', $allocateId)->first();

        if ($old_allocate == null) {
            return response()->json(ResponseModel::Failed(null, $allocateId, "Old Allocate Id Not Found"));
        }

        $allocate = [
            "name"=> Str::uuid7(),
            "student_id" => $requested_vars['student_id'],
            "tutor_id" => $requested_vars['tutor_id'],
            "staff_id"=>1, // this should get from webapplication.
            "section_id" => $old_allocate->section_id,
            "allocation_date"=> $requested_vars['allocation_date'],
            "allocated_by"=>"Hello World " // this should get from staff table
        ];
        DB::beginTransaction();
        try {
            $data = Allocation::create($allocate);
            $old_allocate->delete();
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