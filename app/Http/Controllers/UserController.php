<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_admin' => false
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $token = auth()->claims([
                    'id' => $user->id,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                ])->attempt($credentials);

            } else {
                return;
            }
            return response()->json(['message' => 'User Logged in successfully','accessToken' => $token], 200);

        }
    }

    public function profile($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendErrorResponse('User not found', null, 404);
        }

        return $user;
    }

    public function registerAdmin(Request $request)
    {
        $validator = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_admin' => true
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function allUsers(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $pageNumber = $request->input('pageNumber', 1);

        $users = User::paginate($pageSize, ['*'], 'page', $pageNumber);

        return $users;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendErrorResponse('User not found', null, 404);
        }

        $user->delete();

        return $this->sendSuccessResponse('User deleted successfully', ['user' => $user]);
    }

    // Helper method for sending success responses
    private function sendSuccessResponse($message, $data = null, $status = 200)
    {
        return response()->json(['message' => $message, 'data' => $data], $status);
    }

    // Helper method for sending error responses
    private function sendErrorResponse($message, $errors = null, $status = 422)
    {
        return response()->json(['message' => $message, 'errors' => $errors], $status);
    }
}
