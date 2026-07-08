<?php

namespace App\Service\Auth;

use App\Mail\RecoverPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class recoveryService
{

    public static $rulesUpdate = [
        'token' => 'required|string',
        'password'  => 'required|string|min:8|confirmed',
    ];

    //
    public function sendEmail(Request $request)
    {
        $email = $request->input('email');

        // Verificar que el email exista en la base de datos
        if (!$this->validateEmail($email)) {
            return response()->json([
                "status" => "false",
                "message" => "Email no enocontrado"
            ], 404);
        }
        //Log::info('Email encontrado en la base de datos: ' . $email);

        $this->send($email);

        return response()->json([
            "status" => "true",
            "message" => "Correo enviado correctamente",
            "email" => $email
        ], 200);
    }

    public function validateEmail(String $email): bool
    {
        return User::where('email', $email)->exists();
    }

    private function send(String $email)
    {
        // Crear token de recuperación de contraseña
        $token = $this->createToken($email);

        //
        Log::info('Enviando correo de recuperación. Token (preview): ' . substr($token, 0, 8) . '...');
        // Enviar correo de recuperación de contraseña
        Mail::to($email)->send(new RecoverPasswordMail($token));
    }

    private function createToken(String $email): string
    {
        // Verificar si ya existe un token para este email
        $oldToken = DB::table('password_resets')->where('email', $email)->first();

        // Si ya existe un token, reutilizarlo
        if ($oldToken && isset($oldToken->token)) {
            return (string) $oldToken->token;
        }

        // Si no existe un token, crear uno nuevo
        $token = Str::random(60);
        // Guardar el token usando el método saveToken
        $this->saveToken($token, $email);

        return $token;
    }

    private function saveToken(String $token, String $email)
    {
        // Guardar el token en la tabla password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );
    }



    public function updatePassword(Request $request)
    {
        $data = $request->validate(self::$rulesUpdate);

        // Verificar si el token existe y es válido
        $token = DB::table('password_resets')->where('token', $data['token'])->first();
        if (!$token) {
            return response()->json([
                "status" => "false",
                "message" => "Token inválido o inexistente."
            ], 400);
        }

        //los token solo tienen duracion de 60 minutos
        $expire = config('auth.password.users.expire', 60);

        //Verificar hora de creacion del token si aun es valido
        if(Carbon::parse($token->created_at)
            ->diffInMinutes(Carbon::now()) > $expire) {
            DB::table('password_resets')->where('email', $token->email)->delete();
            return [
                "status" => "false",
                "message" => "Token Expirado"
            ];
        }

        // Actualizar la contraseña del usuario
        $user = User::where('email', $token->email)->first();

        if (!$user) {
            return response()->json([
                "status" => "false",
                "message" => "Usuario no encontrado."
            ], 404);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        // Eliminar token usado
        DB::table('password_resets')->where('email', $token->email)->delete();

        return response()->json([
            "status" => "true",
            "message" => "Contraseña actualizada correctamente."
        ], 200);
    }
}
