<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
    
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Failed Validation',
                'data' => $validator->getMessageBag()
            ], 422);
        }
    
        // Coba login dan buat token JWT
        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email or Password is wrong'
            ], 401);
        }
    
        $user = Auth::user();
        
        $refresh = JWTAuth::fromUser($user);
    
        $cookie = cookie(
            'refresh_token',
            $refresh,
            60 * 24 * 7, // 7 hari
            '/',
            null,
            true, // secure
            true, // httpOnly
            false,
            'None'
        );
        if ($user->hasRole(['admin', 'editor'])) {
            return $this->respondWithToken($token)->withCookie($cookie);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'Anda tidak memiliki akses'
        ], 403);

    }

    public function logout(Request $request)
    {   
        try {

            if(auth()->user()) {
                auth()->logout();

                return response()->json([
                    'status' => true,
                    'message' => 'Logout berhasil'
                ], 200);
            } else {
                return $this->unauthorize();
            }
    
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function unauthorize()
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorize',
        ], 401);
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');

            if($refreshToken) {
                return $this->respondWithToken(auth()->refresh());
            }

        } catch (\Throwable $th) {

            return $this->unauthorize();
        }
    }

    public function me()
    {
        try {
            if(auth()->user()) {

                $user = auth()->user();

                $rolesName = $user->roles()->pluck('name');

                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'roles' => $rolesName
                ]);
            } else {
                return $this->unauthorize();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }
}
