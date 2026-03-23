<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RecoverPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RecoveryPasswordController extends Controller
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
                "status" => "error",
                "message" => "Email no enocontrado"
            ],404);
        }

        $this->send($email);

        return response()->json([
            "status" => "success",
            "message" => "Correo enviado correctamente",
            "email" => $email
        ], 200);
    }

    public function send($email)
    {
        // Crear token de recuperación de contraseña
        $token = $this->createToken($email);

        //
        Log::info('Enviando correo de recuperación. Token (preview): ' . substr($token, 0, 8) . '...');
        // Enviar correo de recuperación de contraseña
        Mail::to($email)->send(new RecoverPasswordMail($token));
    }

    public function createToken($email)
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

    public function saveToken($token, $email)
    {
        // Verificar si ya existe un registro para este email
        $exists = DB::table('password_resets')->where('email', $email)->exists();

        // Si existe, actualizar el token y la fecha de creación; si no, crear un nuevo registro
        if ($exists) {
            DB::table('password_resets')->where('email', $email)->update([
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        } else {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        }
    }

    public function validateEmail($email)
    {
        return User::where('email', $email)->exists(); //->exists()
    }

    public function updatePassword(Request $request)
    {
        // Validar datos de entrada
        $data = $request->validate(self::$rulesUpdate);

        // Verificar que el token exista y sea válido
        $oldToken = DB::table('password_resets')->where('token', $data['token'])->first();

        // Si el token no existe, retornar error
        if (!$oldToken) {
            return response()->json(['message' => 'Token inválido o inexistente.'], 400);
        }

        //los token solo tienen duracion de 60 minutos
        $expire = config('auth.password.users.expire', 60);
        $createAt = Carbon::parse($oldToken->created_at);
        
        //verificar hora de creacion del token si aun es valido
        if (Carbon::now()->diffInMinutes($createAt) > $expire) {
            DB::table('password_resets')->where('email', $oldToken->email)->delete();
            return response()->json([
                'message' => 'Token Expirado'
            ]);
        }

        // Buscar el usuario asociado al token
        $user = User::where('email', $oldToken->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        // Actualizar la contraseña del usuario
        $user->password = Hash::make($data['password']);
        $user->save();

        // Eliminar token usado
        DB::table('password_resets')->where('email', $oldToken->email)->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente.'], 200);
    }
}
