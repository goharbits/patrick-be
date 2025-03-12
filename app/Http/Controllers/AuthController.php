<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'login' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::validationFailed($validator->errors());
            }
            $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';


            if (!Auth::attempt([$loginType => $request->login, 'password' => $request->password])) {
                return ResponseHelper::unauthorized('Invalid credentials, please check your email and password.');
            }
            $user = Auth::user();
            if ($user->blocked) {
                Auth::logout();
                return ResponseHelper::unauthorized('Your account is blocked. Please contact customer support.');
            }
            $token = $user->createToken('Patrick')->plainTextToken;
            return ResponseHelper::success([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->blocked ? 'Blocked' : 'Unblocked',
                    'subscription_status' => $user->subscription_status
                ]
            ], 'Login successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred during login', 500, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return ResponseHelper::unauthorized('User not authenticated');
            }
            $user->tokens()->delete();
            return ResponseHelper::success(null, 'Logout successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred during logout', 500, $e->getMessage());
        }
    }

    public function sendResetPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationFailed($validator->errors());
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ResponseHelper::error('Email does not exist', 404);
        }
        $token = Str::random(60);
        $isUpdated = PasswordReset::where('email', $user->email)
            ->update([
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        if (!$isUpdated) {
            PasswordReset::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        }
        try {
            Mail::send('emails.custom_reset', ['token' => $token, 'email' => $user->email], function ($message) use ($user) {
                $message->subject('Reset Your Password');
                $message->to($user->email);
            });
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to send email', 500, $e->getMessage());
        }
        return ResponseHelper::success(null, 'Password reset email sent successfully. Please check your inbox.');
    }

    public function resetPassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationFailed($validator->errors());
        }
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset) {
            return ResponseHelper::error('Invalid or expired token', 404);
        }
        $tokenExpiry = Carbon::parse($passwordReset->created_at)->addMinutes(60);
        if (Carbon::now()->greaterThan($tokenExpiry)) {
            return ResponseHelper::error('Token has expired', 400);
        }
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return ResponseHelper::error('User not found', 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        PasswordReset::where('email', $user->email)->delete();
        return ResponseHelper::success(null, 'Password has been reset successfully');
    }

    public function getProfile(){
        $user =  Auth::user()->load('userSubscription');
        return ResponseHelper::success(
            $user
        , 'User Data Found');
    }

        public function updateProfile(Request $request){

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);


        if ($validator->fails()) {
            return ResponseHelper::validationFailed($validator->errors()->first());
        }

        $user =  Auth::user()->load('userSubscription');

        if (!Hash::check($request->current_password, $user->password)) {
            return ResponseHelper::validationFailed('Current password is incorrect');
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        return ResponseHelper::success(
            $user
        , 'User Updated Successfully');

    }
}
