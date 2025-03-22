<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingStudentRequest;
use App\Models\Allocation;
use App\Models\Arranging;
use App\Models\MeetingDetail;
use App\Models\MeetingRequest;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

class MeetingRequestController extends Controller
{
    public function index(int $arrange_id){
        $allocation = Arranging::find($arrange_id)->first();
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
        $meeting_detail = MeetingDetail::where('arrange_id',$arrange_id)->where('status','pending')->first();
        $validated = $request->safe();
        $student_id = auth()->user()->id;
        $meeting_request = [
            "student_id"=>$student_id,
            "arrange_date"=>Carbon::parse($validated['arrange_date']),
            "tutor_id"=> $validated['tutor_id'],
            "reason"=> $validated['reason'],
            "topic"=>$meeting_detail['topic'],
            "meeting_type"=>$validated['meeting_type'],
            "location"=> $validated['location'],
            "online_meeting_applicaiton"=>$validated['meeting_app'],
            "approved"=>false,
            "status"=>"pending",
            "approved_arrange_id"=>$arrange_id
        ];
        DB::beginTransaction();
        try{
            MeetingRequest::where('approved_arrange_id',$arrange_id)->where('status','pending')->update([
                'status'=>'cancelled',
                'updated_at'=> Carbon::now("UTC")
            ]);
            $model = MeetingRequest::create(attributes: $meeting_request);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        
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
        $validated_data = request()->validate([
            'reject_reason' => 'nullable|string'
        ]);
        $sample =[];
        $model = MeetingRequest::where('id',$id)->first();
        $sample['status'] = "reject";
        $sample['approved_reject_date'] = Carbon::now('UTC');
        $sample['rejcet_reason'] = array_key_exists('reject_reason',$validated_data) ? $validated_data['reject_reason'] : "";
        $sample['updated_at'] = Carbon::now('UTC');
        $model->update($sample);
        return response()->json(ResponseModel::Ok($model,$model->id,"Rejected Successfully"));
    }

    public function approveRequest(int $id){
        if((!auth()->user()->hasAnyRole('tutor'))){
            return response()->json(ResponseModel::Failed(null,"","failed"));
        }

        $validated_data = request()->validate([
            'link' => 'nullable|string', 'description' => 'nullable|string'
        ]);

        $model = MeetingRequest::where('id',$id)->first();
        $sample['status'] = "approved";
        $sample['approved_reject_date'] = Carbon::now('UTC');
        $sample['rejcet_reason'] = "";
        $sample['updated_at'] = Carbon::now('UTC');
        $model->update($sample);
        $oldMeetingDetail= MeetingDetail::where("arrange_id",$model->approved_arrange_id)->first();
        $meetingDetail = [
            "arrange_date"=>$model->arrange_date,
            "meeting_type"=> $model->meeting_type,
            "topic"=>$model->topic,
            "location"=>$model->location,
            "online_meeting_applicaiton"=>$model->online_meeting_application,
            "arrange_id"=>$model->approved_arrange_id,
            "status"=>"pending",
            "meeting_link"=>array_key_exists('link',$validated_data) ? $validated_data['link'] : $model->link,
            "description"=> array_key_exists('description',$validated_data) ? $validated_data['description'] : $model->description
        ];
        DB::beginTransaction();
        try{
            $oldMeetingDetail->update([
                "status"=>"rescheduled"
            ]);
            $meetingDetail = MeetingDetail::create($meetingDetail);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        
        return response()->json(ResponseModel::Ok($meetingDetail,$model->id,"Approved Successfully"));
    }

}
