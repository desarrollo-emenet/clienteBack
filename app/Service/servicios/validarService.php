<?php

namespace App\Service\servicios;

use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class validarService
{
    protected $consultaApiService;

    public function __construct(consultaApiService $consultaApiService)
    {
        $this->consultaApiService = $consultaApiService;
    }


    public function validarClienteCompleto(string $numeroCliente)
    {
        //Descodificar número
        /*$numeroCliente = codificacionService::descodificarClienteConLetra($numEncriptado);
        if ($numeroCliente instanceof JsonResponse) {
            return $numeroCliente; // Error en descodificación
        }*/

        // Validar con API
        $clienteData = $this->validarClienteAPI($numeroCliente, true);
        if ($clienteData instanceof JsonResponse) return $clienteData; // Error en API


        $clienteEmail = $this->obtenerEmail($clienteData);
        if ($clienteEmail instanceof JsonResponse) return $clienteEmail; // Error al obtener email

        // retornar datos
        return [
            'cliente' => $numeroCliente,
            'email' => $clienteEmail
            // 'clienteData' => $clienteData,
        ];
    }

    //Validar numero de cliente con la API
    public function validarClienteAPI(string $numeroCliente, bool $verificarBaja = true)
    {
        $clienteData = $this->consultaApiService->peticionAPI($numeroCliente, 'true');
        if (!$clienteData) return response()->json([
            'success' => "error",
            'message' => 'Error al obtener datos externos',
        ], 422);

        //Verificar clasificación de baja
        if (!$verificarBaja) return $clienteData;

        $clasificacion = $clienteData['cliente']['clasificacion'] ?? null;
        if ($clasificacion == 'BAJA') return response()->json([
            'success' => "error",
            'message' => 'Este servicio está dado de baja y no puede registrarse'
        ], 422);

        return $clienteData;
    }

    public static function obtenerEmail(array $clienteData): ?string
    {
        // Extraer el email del clienteData
        //$email = $clienteData['cliente']['email'] ?? null;

        $email = "crismart12ne@gmail.com"; // Email fijo para pruebas

        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) return response()->json([
            'success' => "error",
            'message' => 'El correo del cliente no es valido o no esta registrado.'
        ], 422);

        /*$userExistente = User::where('email', $email)->exists();

        // Si el email ya existe, retornar un error
        if ($userExistente) {
            return response()->json([
                'message' => 'Este correo ya está registrado',
            ], 409);
            //throw new Exception('Este correo ya está registrado');
        }*/

        return $email;
    }
}
