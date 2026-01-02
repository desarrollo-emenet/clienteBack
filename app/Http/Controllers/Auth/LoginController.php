<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;
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
            'cliente' => ['nullable', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = null;

        //login con correo  
        if(!empty($credentials['email'])){
            $user = User::where('email', $credentials['email'])->first();

        //login con numero de cliente
        }elseif (!empty($credentials['cliente'])){
            $service = Service::where('numero_cliente', $credentials['cliente'])->first();

            if($service){
                $user = $service->user;
            }
        }

        //usuario no encontrado o contraseña incorrecta
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        //si el correo esta verificado
        if(!$user->hasVerifiedEmail()){
            return response()->json(['message' => 'Cuenta no verificada'], 403);
        }

        //creacion de token
        //$user->tokens()->delete();
        $tokenName = $credentials['email'] ?? $credentials['cliente'];
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            "message" => "Login Successful",
            "token" => $token
        ]);
    }
}
