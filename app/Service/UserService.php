<?php

namespace App\Service;

use App\Models\Service;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class UserService
{
    // 
    public function existeCliente(string $numeroCliente, $email)
    {
        //Validar si existe el numero de cliente
        $serviceExistente = Service::where('numero_cliente', $numeroCliente)->first();

        //si existe, revisa si esta verificado el email sino reenvia correo
        if ($serviceExistente) {
            return $this->esVerificado($serviceExistente);
        }

        //si no existe, crear cliente
        return $this->crearCliente($numeroCliente, $email);
    }

    public function esVerificado($serviceExistente)
    {
        // Buscar si ya existe el número de cliente
        $user = User::find($serviceExistente->user_id);

        // Si NO ha verificado correo
        if (is_null($user->email_verified_at)) {

            // Generar nueva contraseña temporal
            $passwordTemporal = Random::generate(8);
            $user->password = Hash::make($passwordTemporal);
            $user->save();


            // Reenviar correo
            $user->notify(new VerifyEmailNotification($passwordTemporal));

            return response()->json([
                'mensaje' => 'Ya existe una cuenta sin verificar. Se ha reenviado el correo.',
            ], 200);
        }

        // Si YA está verificado
        return response()->json([
            'message' => 'Este número de cliente ya está registrado y verificado.',
        ], 409);
    }


    public function crearCliente(string $numeroCliente, string $email)
    {
        $passwordTemporal = Random::generate(8);

        $user = DB::transaction(function () use ($numeroCliente, $email, $passwordTemporal) {

            //guardar en tabla users email y password
            $user = User::create([
                'email'    => $email,
                'password' => Hash::make($passwordTemporal), //contraseña temporal
            ]);
            //guardar en tabla services numero de cliente
            Service::create([
                'numero_cliente' => $numeroCliente,
                'user_id' => $user->id,
            ]);

            //Evento de registro 
            $user->notify(new VerifyEmailNotification($passwordTemporal));

            return $user;
        });
        return response()->json([
            'mensaje' => 'Registro creado correctamente',
            'user'    => $user,
        ], 201);
    }  

    public function mantenerDatos(){
        //fallback
        //actualizacion de dato con TTL para mantener la sesion del cliente activa y evitar bloqueos por inactividad
    }
}
