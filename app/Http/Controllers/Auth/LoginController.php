<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['nullable', 'email'],
            'cliente' => ['nullable', 'string', 'max:5'],
            'password' => ['required', 'string'],
        ]);

        $user = null;

        if(!empty($credentials['email'])){
            $user = User::where('email', $credentials['email'])->first();
        }elseif (!empty($credentials['cliente'])){
            $user = User::where('cliente', $credentials['cliente'])->first();
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        
        if(!$user->hasVerifiedEmail()){
            return response()->json(['message' => 'Cuenta no verificada'], 403);
        }

        //$user->tokens()->delete();
        $tokenName = $credentials['email'] ?? $credentials['cliente'];
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            "message" => "Login Successful",
            "token" => $token
        ]);
    }
}
