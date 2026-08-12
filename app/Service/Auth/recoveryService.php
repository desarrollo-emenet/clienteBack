<?php

namespace App\Service\Auth;

use App\Mail\RecoverPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class recoveryService
{
    public function sendEmail($request)
    {
        $email = $request->input('email');

        // Verificar que el email exista en la base de datos
        if (!$this->validateEmail($request->email))  return response()->json([
            "status" => "error",
            "message" => "La dirección de correo electrónico no está registrada."
        ], 404);
        //Log::info('Email encontrado en la base de datos: ' . $email);

        $this->send($email);

        return response()->json([
            "status" => "success",
            "message" => "Se ha enviado un correo de recuperación a la dirección de correo electrónico proporcionada.",
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

        //Log::info('Enviando correo de recuperación. Token: ' . substr($token, 0, 8) . '...');
        // Enviar correo de recuperación de contraseña
        Mail::to($email)->send(new RecoverPasswordMail($token));
    }

    private function createToken(String $email): string
    {
        // Verificar si ya existe un token para este email
        //$oldToken = DB::table('password_resets')->where('email', $email)->first();

        // Si ya existe un token, reutilizarlo
        /*if ($oldToken && isset($oldToken->token)) {
            return (string) $oldToken->token;
        }*/

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

    public function validarToken($request)
    {
        $resultado = $this->ObtenerTokenValidad($request->token);

        if (!$resultado) return response()->json([
            "status" => false,
            "message" => "Enlace invalido"
        ], 400);

        return response()->json([
            "status" => true,
            "message" => "Token válido."
        ], 200);
    }

    private function ObtenerTokenValidad(string $token)
    {

        $passwordReset = DB::table('password_resets')->where('token', $token)->first();
        //sino existe
        if (!$passwordReset) return null;

        //tiempo de expiracion
        $expire = config('auth.password.users.expire', 60);

        //verificar tiempo
        if (Carbon::parse($passwordReset->created_at)->addMinutes($expire)->isPast()) {
            // Eliminar el token expirado
            DB::table('password_resets')->where('email', $passwordReset->email)->delete();
            return null;
        }

        return $passwordReset;
    }


    public function updatePassword($request)
    {
        $token = $this->ObtenerTokenValidad($request->token);
        if (!$token) return response()->json([
            "status" => "error",
            "message" => "El enlace anterior no es válido, envia una nueva solicitud"
        ], 404);
        // Actualizar la contraseña del usuario
        $user = User::where('email', $token->email)->first();
        if (!$user) return response()->json([
            "status" => "error",
            "message" => "Usuario no encontrado."
        ], 404);

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('email', $token->email)->delete();

        DB::commit();
        return response()->json([
            "status" => "success",
            "message" => "Contraseña actualizada correctamente."
        ], 200);
    }
}
