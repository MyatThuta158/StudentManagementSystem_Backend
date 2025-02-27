<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        //   dd($request->all());

        try {
            $validate = $request->validate([
                'email'    => 'required|email|string',
                'password' => 'required|string',
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => $e], 500);
        }
        //---This is for login process---//
        try {
            $email    = $validate['email'];
            $password = $validate['password'];

            //-----This check the admin table first----//
            $admin = \App\Models\Staff::where('email', $email)->first();

            //dd($admin && Hash::check($password, $admin->Password));

            if ($admin && Hash::check($password, $admin->password)) {
                $token = $admin->createToken('token')->plainTextToken;

                                                            //dd($token);
                $cookie = cookie('token', $token, 60 * 24); // 1 day
                                                            // dd($cookie);
                return response()
                    ->json(['message' => 'Admin login successful!', 'token' => $token, 'role' => 'admin'])
                    ->withCookie($cookie);
            }

            $tutor = \App\Models\Tutor::where('email', $email)->first();

            // dd($user);
            if ($tutor && Hash::check($password, $tutor->password)) {
                // Generate token for user
                $token  = $tutor->createToken('user_token')->plainTextToken;
                $cookie = cookie('token', $token, 60 * 24); // 1 day
                return response()
                    ->json(['message' => 'Tutor login successful!', 'token' => $token, 'role' => 'tutor'])
                    ->withCookie($cookie);
            }

            $student = \App\Models\Student::where('email', $email)->first();

            // dd($user);
            if ($student && Hash::check($password, $student->password)) {
                // Generate token for user
                $token  = $student->createToken('user_token')->plainTextToken;
                $cookie = cookie('token', $token, 60 * 24); // 1 day
                return response()
                    ->json(['message' => 'Student login successful!', 'token' => $token, 'role' => 'student'])
                    ->withCookie($cookie);
            }

            return response()->json(['message' => 'Invalid login credentials'], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
