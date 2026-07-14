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


    private function ObtenerTokenValidad(string $token)
    {

        $passwordReset = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        //sino existe
        if (!$passwordReset) {
            response()->json([
                'status' => false,
                'messaje' => 'Ya se uso el enlace o no existe'
            ], 400);
            return null;
        }

        //tiempo de expiracion
        $expire = config('auth.password.users.expire', 60);

        //verificar tiempo
        if (
            Carbon::parse($passwordReset->created_at)
            ->addMinutes($expire)
            ->isPast()
        ) {
            // Eliminar el token expirado
            DB::table('password_resets')
                ->where('email', $passwordReset->email)
                ->delete();

            response()->json([
                'status' => false,
                'message' => 'El enlace ha expirado.',
            ], 400);
            return null;
        }

        return $passwordReset;
    }
    public function validarToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $resultado = $this->ObtenerTokenValidad($request->token);

        if (!$resultado) {
            return response()->json([
                "status" => false,
                "message" => "enlace invalido"
            ], 400);
        }

        return response()->json([
            "status" => true,
            "message" => "Token válido."
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate(self::$rulesUpdate);

        $tokenData = $this->ObtenerTokenValidad($data['token']);

        if (!$tokenData) {
            return response()->json([
                "status" => false,
                "message" => "enlace invalido"
            ], 400);
        }

        $token = $tokenData;

        // Actualizar la contraseña del usuario
        $user = User::where('email', $token->email)->first();

        if (!$user) {
            return response()->json([
                "status" => "false",
                "message" => "Usuario no encontrado."
            ], 404);
        }

        DB::transaction(function () use ($user, $data, $token) {

            $user->update([
                'password' => Hash::make($data['password'])
            ]);

            DB::table('password_resets')
                ->where('email', $token->email)
                ->delete();
        });

        return response()->json([
            "status" => "true",
            "message" => "Contraseña actualizada correctamente."
        ], 200);
    }
}
