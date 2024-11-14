<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Http\Requests\Auth\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AuthController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware();
    // }

    // public static function middleware()
    // {
    //     return [
    //         new Middleware('auth:api', except: ['login', 'register']),
    //     ];
    // }

    public function register(RegisterRequest $request) {
        try {
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database error'. $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred during registration: '. $e->getMessage()
            ], 500);
        }
    }
    
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if(!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 401);
            }

            return $this->responseWithToken($token);
        } catch (JWTException $e) {
            return response()->json([
                'error' => ''
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occured during login: '. $e->getMessage()
            ], 500);
        }
        
    }

    public function me()
    {
        // $auth = user();
        return response()->json(auth()->user());
    }

    public function refresh()
    {
        return $this->responseWithToken(auth()->refresh());
    }

    public function responseWithToken($token)
    {
        return response()->json([
            'access_ token' => $token,
            'token_type' => 'bearer',
            'expire_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

}
