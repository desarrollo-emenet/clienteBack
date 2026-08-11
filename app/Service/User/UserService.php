<?php

namespace App\Service\User;

use App\Models\Service;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Service\servicios\validarService;
use Nette\Utils\Random;

class UserService
{
    protected $validarService;

    public function __construct(validarService $validarService)
    {
        $this->validarService = $validarService;
    }

    public function existeCliente(string $numeroCliente)
    {
        // Validar el cliente con el servicio
        $validacion = $this->validarService->validarClienteCompleto($numeroCliente);

        // Si la validación devuelve un error, retornar esa respuesta
        if ($validacion instanceof \Illuminate\Http\JsonResponse) {
            Log::error('Error al validar cliente: ' . $validacion->getContent());
            return $validacion;
        }

        $email = $validacion['email']; // Extraer el email del cliente validado
        //Validar si existe el numero de cliente
        $serviceExistente = Service::where('numero_cliente', $numeroCliente)->first();
        //log::info('existeCliente', ['numeroCliente' => $numeroCliente, 'email' => $email]);

        //si existe, revisa si esta verificado el email sino reenvia correo
        /*if ($serviceExistente) {
            return $this->esVerificado($serviceExistente, $email);
        }*/

        //si no existe, crear cliente
        //return $this->crearCliente($numeroCliente, $email);

        if ($serviceExistente) {
            return $this->esVerificado($serviceExistente, $email);
        }
        return $this->crearCliente($numeroCliente, $email);
    }

    public function esVerificado($serviceExistente, string $email)
    {
        // Buscar si ya existe el número de cliente
        $user = User::find($serviceExistente->user_id);
        // Si no ha verificado el correo
        if (is_null($user->email_verified_at)) {
            // Generar nueva contraseña temporal
            $passwordTemporal = Random::generate(8);

            $user->update([
                'email' => $email,
                'password' => Hash::make($passwordTemporal),
            ]);

            // Reenviar correo
            $user->notify(new VerifyEmailNotification($passwordTemporal));
            DB::commit();
            return response()->json([
                'status' => "success",
                'message' => 'Ya existe una cuenta sin verificar. Se ha reenviado el correo.',
            ], 200);
        }
        // Si YA está verificado
        DB::commit();
        return response()->json([
            'status' => "success",
            'message' => 'Este número de cliente ya está registrado y verificado.',
        ], 409);
    }


    public function crearCliente(string $numeroCliente, string $email)
    {
        $passwordTemporal = Random::generate(8);
        log::info('crearCliente', ['numeroCliente' => $numeroCliente, 'email' => $email, 'passwordTemporal' => $passwordTemporal]);
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
        DB::commit();
        return response()->json([
            'mensaje' => 'Registro creado correctamente',
            'user'    => $user,
        ], 201);
    }
}
