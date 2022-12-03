<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Unauthorized: Authentication Failed', 401);
            }

            $query_user = User::where('email', $request->email);
            if (!Hash::check($request->password, $query_user->value('password'))) {
                throw new \Exception('Invalid Credentials');
            }

            $user = $query_user->first();
            $token_result = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $token_result,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated: Login Success');
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                // [
                //     'message' => 'Something went wrong',
                //     'error' => $th
                // ], 
                $th ?? 'Authentication Failed');
        }
    }
}
