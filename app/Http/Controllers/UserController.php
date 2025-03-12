<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Events\UserStatusChanged;
use App\Mail\UserCredentialsMail;
use App\Mail\UserRegisterMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        try {
            $users = User::whereNot('role','admin')->with(['userSubscription'])->get();
            return ResponseHelper::success($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve users', 500, $e->getMessage());
        }
    }

    public function store(Request $request)
    {

        try {
            $register =  $request->register;
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:users|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|max:255',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation failed', 422, $validator->errors());
            }

            DB::beginTransaction();
            $plainPassword = $request->password;
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($plainPassword),
                'role' => 'user',
            ]);
            try{
                if($register){
                    Mail::to($user->email)->send(new UserRegisterMail($user));
                }else{
                    Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
                }
            } catch (\Exception $e) {

            }
            DB::commit();
            if($register){

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

                $user = Auth::user();
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
                    ], 'Register and Login successful');
            }else{
                return ResponseHelper::error( 'User Logged in failed but registered successfully!');
            }

            }else{
                return ResponseHelper::success($user, 'User created successfully and email sent!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::error('An error occurred while creating the user', 500, $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $user = User::where('id',$id)->with(['userSubscription'])->first();
            return ResponseHelper::success($user, 'User retrieved successfully!');
        } catch (\Exception $e) {
            return ResponseHelper::error('User not found', 404, $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|string|min:8',
            ]);

            $user->fill($validatedData);
            if (isset($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }
            $user->save();
            return ResponseHelper::success($user, 'User updated successfully!');
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred while updating the user', 500, $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return ResponseHelper::success(null, 'User deleted successfully!', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred while deleting the user', 500, $e->getMessage());
        }
    }

    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        $user->blocked = true;
        $user->save();

        // event(new UserStatusChanged($user, 'Your account has been blocked.'));
        UserStatusChanged::dispatch($user, 'Your account has been blocked.');
        return ResponseHelper::success($user, 'User blocked successfully.');
    }
    public function unblockUser($id)
    {
        $user = User::findOrFail($id);
        $user->blocked = false;
        $user->save();
        UserStatusChanged::dispatch($user, 'Your account has been unblocked.');
        return ResponseHelper::success($user, 'User unblocked successfully.');
    }
}
