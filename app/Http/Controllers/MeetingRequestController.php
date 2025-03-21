<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingStudentRequest;
use App\Models\Allocation;
use App\Models\MeetingDetail;
use App\Models\MeetingRequest;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingRequestController extends Controller
{
    public function index(int $allocation_id){
        $allocation = Allocation::find($allocation_id);
        $student_id = $allocation->student_id;
        $tutor_id = $allocation->tutor_id;
        $models = MeetingRequest::where('student_id',$student_id)->where('tutor_id',$tutor_id)->get();
        return response()->json(ResponseModel::Ok($models,"","Fetch Successfully!"));
    }
    public function create(int $arrange_id,MeetingStudentRequest $request)
    {
        if((!auth()->user()->hasAnyRole('student'))){
            return response()->json(ResponseModel::Failed(null,"","failed",));
        }
        $validated = $request->safe();
        $student_id = auth()->user()->id;
        $meeting_request = [
            "student_id"=>$student_id,
            "tutor_id"=> $validated['tutor_id'],
            "reason"=> $validated['reason'],
            "topic"=>$validated['topic'],
            "meeting_type"=>$validated['meeting_type'],
            "location"=> $validated['location'],
            "meeting_app"=>$validated['meeting_app'],
            "approve"=>false,
            "approved_arrange_id"=>$arrange_id
        ];
        $model = MeetingRequest::create($meeting_request);
        return response()->json(ResponseModel::Ok($model,$model->id,"Requested successfully"));
    }

    public function cancelRequest(int $id){
        if((!auth()->user()->hasAnyRole('student'))){
            return response()->json(ResponseModel::Failed(null,"","failed"));
        }
        $model = MeetingRequest::where('id',$id)->first();
        $model->status = "cancelled";
        $model->updated_at = Carbon::now('UTC');
        $model->save();
        return response()->json(ResponseModel::Ok($model,$model->id,"Cancelled Successfully"));
    }

    public function rejectRequest(int $id){
        if((!auth()->user()->hasAnyRole('tutor'))){
            return response()->json(ResponseModel::Failed(null,"","failed"));
        }
        $model = MeetingRequest::where('id',$id)->first();
        $model->status = "reject";
        $model->approved_reject_date = Carbon::now('UTC');
        $model->rejcet_reason = "";
        $model->updated_at = Carbon::now('UTC');
        $model->save();
        return response()->json(ResponseModel::Ok($model,$model->id,"Rejected Successfully"));
    }

    public function approveRequest(int $id){
        if((!auth()->user()->hasAnyRole('tutor'))){
            return response()->json(ResponseModel::Failed(null,"","failed"));
        }
        $model = MeetingRequest::where('id',$id)->first();
        $model->status = "approved";
        $model->approved_reject_date = Carbon::now('UTC');
        $model->rejcet_reason = "";
        $model->updated_at = Carbon::now('UTC');
        $model->save();
        $meetingDetail = [
            "arrange_date"=>$model->arrange_date,
            "meeting_type"=> $model->meeting_type,
            "topic"=>$model->topic,
            "location"=>$model->location,
            ""=>"",
            "meeting_link"=>$model->meeting_link,
            "details"=>"Hello"
        ];
        return response()->json(ResponseModel::Ok($model,$model->id,"Rejected Successfully"));
    }

}
