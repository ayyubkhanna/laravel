<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Failed Validation', 'data' => $validator->getMessageBag()], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if($user->hasRole('admin')){
                return response()->json(['message' => 'Anda tidak memiliki Akses'], 403);
            }

            // Membuat token untuk user yang berhasil login
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Success login',
                'data' => $user,
                'token' => $token
            ], 200);
        } else {
            // Jika kredensial salah
            return response()->json([
                'status' => false,
                'message' => 'Email or Password is wrong'
            ], 401);
        }

    }
}
