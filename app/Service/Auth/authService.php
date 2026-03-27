<?php

namespace App\Service\Auth;

use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class authService
{
    public function login($request)
    {
        $user = User::from('users as u')
            ->select('u.*')
            ->join("services as s", 'u.id', 's.user_id')
            ->where('email', $request->cliente)
            ->orWhere('numero_cliente', $request->cliente)->first();

        Log::info($user);

        if (!$user || !Hash::check($request->password, $user->password)) return response()->json([
            'status' => "error",
            'mensaje' => 'El usuario y/o contraseña son incorrectos'
        ], 401);

        if (!$user->hasVerifiedEmail()) return response()->json([
            'status' => "error",
            'mensaje' => 'Cuenta no verificada'
        ], 403);

        $tokenName = $credentials['email'] ?? $request['cliente'];
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            "status" => 'success',
            "mensaje" => "Usuario autenticado",
            "token" => $token
        ], 200);
    }
}
