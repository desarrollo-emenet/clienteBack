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

    public static $rulesUpdate = [
        'email' => 'nullable|email|max:50|unique:users,email,',
        'old_password' => 'required|string',
        'password'  => 'nullable|string|min:8',
    ];

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
        $serviceExistente = Service::where('numero_cliente', $numeroCliente)->first();
        
        $correoExistente = $this->validarService->validarCorreoDisponible($email);
        if ($correoExistente instanceof \Illuminate\Http\JsonResponse) {
            Log::error('Error al validar correo: ' . $correoExistente->getContent());
            return $correoExistente;
        }
        //log::info('existeCliente', ['numeroCliente' => $numeroCliente, 'email' => $email]);

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
        $user->notify(new VerifyEmailNotification($passwordTemporal,null,'create_account'));
        DB::commit();
        return response()->json([
            'mensaje' => 'Registro creado correctamente',
            'user'    => $user,
        ], 201);
    }

    public function update(User $user, $data)
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        //verificar que haya algun dato
        if (empty($email) && empty($password)) return response()->json([
            "status" => "error",
            'message' => 'Debes proporcionar un nuevo correo o una nueva contraseña.'
        ], 422);

        // Verificar la contraseña actual
        $oldPassword = $data['old_password'];
        if (!Hash::check($oldPassword, $user->password)) return response()->json([
            "status" => "error",
            'message' => 'Contraseña actual incorrecta.'
        ], 403);

        //update password
        if (!empty($password)) {
            $user->password = Hash::make($password);
            //si solo se cambia contraseña
            $user->save();
            DB::commit();
            return response()->json([
                'mensaje' => 'Contraseña actualizada correctamente',
                'email_verification_required' => false,
                'data'    => $user->fresh(),
            ], 200);
        }

        //update email
        if (!empty($email)) {

            if (strtolower($email) === strtolower($user->email)) {
                return response()->json([
                    'message' => 'Ya existe este correo, intente con otro.'
                ], 422);
            }

            //actualizar corre hasta que se confirme el correo
            $user->notify(new VerifyEmailNotification(null, $email, 'update_account'));

            //$user->save();
            DB::commit();
            return response()->json([
                'mensaje' =>
                'Se ha enviado un enlace de verificación al nuevo correo.',
                'email_verification_required' => true,
                'data' => $user->fresh(),
            ], 200);
        }
    }
}
