<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\{LoginRequest, RegisterRequest};
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $detail_error = null;
        try {
            $validated = $request->all();

            if (count($request->all()) !== count($validated)) {
                throw new Exception('Invalid Credentials', 400);
            }

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                throw new Exception('Unauthorized', 401);
            }

            $query_user = User::where('email', $request->email);
            if (!$query_user->exists()) {
                throw new Exception('Not Found', 404);
            } 
            else if (!Hash::check($request->password, $query_user->value('password'))) {
                throw new Exception('Invalid Credentials', 400);
            }

            $user = $query_user->first();
            $token_result = $user->createToken(env('TOKEN_SECRET'))->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $token_result,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage() ?? 'Authentication Failed',
                $e->getCode(),
                $detail_error
            );
        }
    }

    public function register(RegisterRequest $request)
    {
        $detail_error = null;
        try {
            $validated = $request->validated();
            $validated['password_confirmation'] = $validated['password'];
            
            if (count($request->all()) !== count($validated)) {
                throw new Exception('Bad Request', 400);
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token_result = $user->createToken(env('TOKEN_SECRET'))->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $token_result,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Registration Success');
        } catch (Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage() ?? 'Registration Failed',
                $e->getCode(),
                $detail_error
            );
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken()->delete();
            
            return ResponseFormatter::success($token, 'Logout Success');
        } catch (Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage() ?? 'Logout Failed',
                $e->getCode()
            );
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Fetch Success');
    }
}
