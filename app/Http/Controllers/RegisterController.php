<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Student;
use App\ResponseModel\ResponseModel;
use Log;

class RegisterController extends Controller
{
    public function register(RegisterRequest $registerRequest){
        try{
            $validatedData = $registerRequest->validated();

            $model = [
                "name" => $validatedData['username'],
                "email"=> $validatedData['email'],
                "password" => bcrypt($validatedData['password']) 
            ]; 
    
            $data = Student::create($model);
    
            return response()->json(ResponseModel::Ok("Registered Successfully", $data->id,"Registered Successfully"));
        }catch(\Exception $e){
            Log::error("RegisterController.register => " + $e->getMessage());
            return response()->json(ResponseModel::Failed("Internal Error","", $e->getMessage()));
        }
    }
}
