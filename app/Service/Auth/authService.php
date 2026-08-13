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
            ->select('u.*', 's.numero_cliente')
            ->join("services as s", 'u.id', 's.user_id')
            ->where('email', $request->cliente)
            ->orWhere('numero_cliente', $request->cliente)
            ->orderBy('s.id', 'asc')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) return response()->json([
            'status' => "error",
            'message' => 'El usuario y/o contraseña son incorrectos'
        ], 401);

        if (!$user->hasVerifiedEmail()) return response()->json([
            'status' => "error",
            'message' => 'La cuenta no ha sido verificada, por favor revisa tu correo electrónico para verificar tu cuenta'
        ], 403);

        //solo 2 sesiones
        $activeTokens = $user->tokens()->count();

        if ($activeTokens >= 2)  $user->tokens()->orderBy('created_at', 'asc')->first()->delete();

        //$tokenName = $request['cliente'];
        $tokenName = $user->numero_cliente;
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            "status" => 'success',
            "message" => "Usuario autenticado",
            "token" => $token,
            "numero_cliente" => $user->numero_cliente
        ], 200);
    }
}
