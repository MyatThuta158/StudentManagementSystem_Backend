<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validate = $request->validate([
                'email'    => 'required|email|string',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e], 500);
        }

        try {
            $email    = $validate['email'];
            $password = $validate['password'];

            //This is for student login//
            $admin = \App\Models\Staff::where('email', $email)->first();
            if ($admin && Hash::check($password, $admin->password)) {
                // Check if it's the first login
                $firstLogin = is_null($admin->last_login_at);
                // Record login time in the admin model
                $admin->last_login_at = now();
                $admin->save();

                // Record login event in logs table
                \App\Models\LoginLog::create([
                    'userable_id'   => $admin->id,
                    'userable_type' => get_class($admin),
                    'browser'       => $request->header('User-Agent'),
                    'ip_address'    => $request->ip(),
                ]);

                $token  = $admin->createToken('token')->plainTextToken;
                $cookie = cookie('token', $token, 60 * 24); // 1 day

                return response()
                    ->json([
                        'message'     => 'Admin login successful!',
                        'token'       => $token,
                        'role'        => 'admin',
                        'first_login' => $firstLogin,
                    ])
                    ->withCookie($cookie);
            }

            //This is for tutor login//
            $tutor = \App\Models\Tutor::where('email', $email)->first();
            if ($tutor && Hash::check($password, $tutor->password)) {
                $firstLogin           = is_null($tutor->last_login_at);
                $tutor->last_login_at = now();
                $tutor->save();

                \App\Models\LoginLog::create([
                    'userable_id'   => $tutor->id,
                    'userable_type' => get_class($tutor),
                    'browser'       => $request->header('User-Agent'),
                    'ip_address'    => $request->ip(),
                ]);

                $token  = $tutor->createToken('user_token')->plainTextToken;
                $cookie = cookie('token', $token, 60 * 24); // 1 day

                return response()
                    ->json([
                        'message'     => 'Tutor login successful!',
                        'token'       => $token,
                        'role'        => 'tutor',
                        'first_login' => $firstLogin,
                    ])
                    ->withCookie($cookie);
            }

            //This is for student login//
            $student = \App\Models\Student::where('email', $email)->first();
            if ($student && Hash::check($password, $student->password)) {
                $firstLogin             = is_null($student->last_login_at);
                $student->last_login_at = now();
                $student->save();

                \App\Models\LoginLog::create([
                    'userable_id'   => $student->id,
                    'userable_type' => get_class($student),
                    'browser'       => $request->header('User-Agent'),
                    'ip_address'    => $request->ip(),
                ]);

                $token  = $student->createToken('user_token')->plainTextToken;
                $cookie = cookie('token', $token, 60 * 24); // 1 day

                return response()
                    ->json([
                        'message'     => 'Student login successful!',
                        'token'       => $token,
                        'role'        => 'student',
                        'first_login' => $firstLogin,
                    ])
                    ->withCookie($cookie);
            }

            return response()->json(
                ['message' => 'Invalid login credentials'],
                \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
