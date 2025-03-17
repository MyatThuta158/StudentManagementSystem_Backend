<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\LoginLog;
use App\Models\Student;
use App\Models\Tutor;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use DB;

class AdminDashboardController{
    public function index(){
        $total_students = Student::count();
        $total_tutors = Tutor::count();
        $total_unassigned_students = Student::whereNotIn('id',function($query){
            $query->select('student_id')->from('allocations');
        })->count();
        $most_used_browsers = LoginLog::groupBy('browser')->get(['browser',DB::raw("COUNT(ip_address)")]);
        $now = Carbon::now();
        $start_of_week = $now->startOfWeek();
        $end_of_week = $now->endOfWeek();
        $most_active_users = Blog::groupBy('author')->with('students')->whereBetween(DB::raw("DATE(created_at)"),[$start_of_week,$end_of_week])->get(['students.name',DB::raw("COUNT(author)"),'author_type']);

        return response()->json(ResponseModel::Ok(["total_students"=>$total_students,"total_tutors"=>$total_tutors,"total_unassigned_students"=>$total_unassigned_students,"browsers"=>$most_used_browsers,$most_active_users],"","dashboard fetched successfully"));
    }
}