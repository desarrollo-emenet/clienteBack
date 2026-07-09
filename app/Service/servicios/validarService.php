<?php

namespace App\Service\servicios;

use App\Models\User;
use Illuminate\Support\Facades\Log;
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
        // Validar con API
        $clienteData = $this->validarClienteAPI($numeroCliente, true);
        //Log::info('clienteData', ['data' => $clienteData]);
        if ($clienteData instanceof JsonResponse) return $clienteData; // Error en API


        $clienteEmail = $this->obtenerEmail($clienteData);
        //Log::info('clienteEmail', ['email' => $clienteEmail]);
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
        ], 404);

        return $clienteData;
    }

    public static function obtenerEmail(array $clienteData): string|JsonResponse
    {
        // Extraer el email del clienteData
        //$email = $clienteData['cliente']['email'] ?? null;

       $email = "crismart12ne@gmail.com"; // Email fijo para pruebas
        //$email = "mcid653@gmail.com";

        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) return response()->json([
            'success' => "error",
            'message' => 'El correo del cliente no es valido o no esta registrado.'
        ], 422);

        return $email;
    }

    public static function validarCorreoDisponible(string $email): ?JsonResponse
    {
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'message' => 'El correo de este cliente ya ha sido registrado',
            ], 409);
        }
        return null;
    }
}
