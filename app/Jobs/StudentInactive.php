<?php

namespace App\Jobs;

use App\Mail\InactiveStudentEmail;
use App\Models\Allocation;
use App\Models\Arranging;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class StudentInactive implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $arranging = Allocation::with(['student','student.blogs' => function($query){
            $query->orderBy("created_at","desc")->first();
        } ,'student.comments'=> function($query){
            $query->orderBy("created_at","desc")->first();
        }])->get();
    
        $before28days = Carbon::now("UTC")->addDays(28);
    
        $arranging->map(function($e) use ($before28days){
            try{
                if($e->student->comments->count()>0 && $e->student->blogs->count()>0) {
                    if(Carbon::parse($e->student->comments[0]['created_at'])->between($before28days,Carbon::now("UTC")) &&  Carbon::parse($e->student->blogs[0]['created_at'])->between($before28days,Carbon::now("UTC"))){
                        $student_job= new SendEmailNotification(new InactiveStudentEmail($e['student']) , $e['student']);
                        dispatch($student_job);
                        # please send mail to student and admin
                    }
                }
                else if($e->student->comments->count()>0) {
                    if(Carbon::parse($e->student->comments[0]['created_at'])->between($before28days,Carbon::now("UTC"))){
                        # please send mail to student and admin
                        $student_job= new SendEmailNotification(new InactiveStudentEmail($e['student']) , $e['student']);
                        dispatch($student_job);
                    }
                }
                else if($e->student->blogs->count()>0) {
                    if(Carbon::parse($e->student->blogs[0]['created_at'])->between($before28days,Carbon::now("UTC"))){
                        # please send mail to student and admin
                        $student_job= new SendEmailNotification(new InactiveStudentEmail($e['student']) , $e['student']);
                        dispatch($student_job);
                    }
                }
                Log::info("Sended to the student " . $e->student->name ." successfully.");
            }catch(Exception $exception){
                Log::error($exception->getMessage());
            }
        });
    }
}