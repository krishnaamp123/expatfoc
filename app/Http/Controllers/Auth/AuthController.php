<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function postLogin()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized. Please check your email and password'
            ], 401);
        }

        // Check the authenticated user's role and redirect accordingly
        $user = auth()->user();

        cookie()->queue(cookie('token', $token, null));

        if ($user->role === 'super_admin') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as Super Admin",
                "token" => $token
            ], 200);
        } elseif ($user->role === 'human_resource') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as Human Resource",
                "token" => $token
            ], 200);
        } elseif ($user->role === 'user_admin') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as User Admin",
                "token" => $token
            ], 200);
        } elseif ($user->role === 'partner') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as Partner",
                "token" => $token
            ], 200);
        } elseif ($user->role === 'manager') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as Manager",
                "token" => $token
            ], 200);
        } elseif ($user->role === 'supervisor') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as Supervisor",
                "token" => $token
            ], 200);
        } elseif ($user->role === 'employee') {
            return response()->json([
                "success" => "Access granted. You're successfully logged in as Employee",
                "token" => $token
            ], 200);
        } else {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Access denied. You do not have the required permissions');
        }
    }

    public function postLogout(Request $request)
    {
        // Hapus JWT (gunakan library atau mekanisme yang Anda pakai)
        auth()->logout();

        // Hapus cookie
        return response()->json([
            'success' => "You're Successfully logged out"
        ], 200)->withCookie(cookie()->forget('token'));
    }

    // Send a password reset link to the given user
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => __($status)
            ], 200);
        } else {
            return response()->json([
                'error' => __($status)
            ], 400);
        }
    }

    // Handle the password reset
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status)
            ], 200);
        } else {
            return response()->json([
                'error' => __($status)
            ], 400);
        }
    }
}
