<?php

namespace App\Service;

use App\Models\Service;
use App\Models\User;
use App\Service\servicios\consultaApiService;
use App\Service\servicios\validarService;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\JsonResponse;

class clientService
{

    protected $validarService;
    protected $consultaApiService;

    public function __construct(validarService $validarService, consultaApiService $consultaApiService)
    {
        $this->validarService = $validarService;
        $this->consultaApiService = $consultaApiService;
    }

    public static function obtenerCliente( $cliente)
    {
        //Descodificar número
        /*$cliente = codificacionService::descodificarClienteConLetra($numEncriptado);
        if ($cliente instanceof JsonResponse) {
            return $cliente; // Error en descodificación
        }*/
        // Realizar petición a la API externa
         $clienteData = consultaApiService::peticionAPI($cliente, 'false');

        if ($clienteData === null) {
            return response()->json([
                'message' => 'Error al obtener datos externos',
            ], 422);
        }

        return $clienteData;
    }

    //Validar numero de cliente con la API
    public static function validarClienteAPI(string $numeroCliente, bool $verificarBaja = true)
    {

        $clienteData = consultaApiService::peticionAPI($numeroCliente, 'false');

        if ($clienteData === null) {
            return response()->json([
                'message' => 'Error al obtener datos externos',
            ], 422);
        }

        //Verificar clasificación de baja
        if ($verificarBaja) {
            $clasificacion = $clienteData['cliente']['clasificacion'] ?? null;

            if ($clasificacion === 'BAJA' || strtoupper($clasificacion) === 'BAJA') {
                return response()->json([
                    'message' => 'Este servicio está dado de baja y no puede registrarse'
                ], 403);
            }
        }
        return $clienteData;
    }


    //verificar si ya existe el cliente en BD
    public static function verificarCliente(string $numCliente): ?JsonResponse
    {
        //revisa el registro de servicios para el numero_cliente encriptado
        if (Service::where('numero_cliente', $numCliente)->exists()) {
            return response()->json([
                'message' => 'El número de cliente ya está registrado en el sistema.'
            ], 409);
        }
        return null;
    }


    public static function validarClienteCompleto(string $numeroCliente)
    {
        //Descodificar número
        /*$numeroCliente = codificacionService::descodificarClienteConLetra($numEncriptado);
        if ($numeroCliente instanceof JsonResponse) {
            return $numeroCliente; // Error en descodificación
        }*/

        // Validar con API
        $clienteData = self::validarClienteAPI($numeroCliente, true);
        if ($clienteData instanceof JsonResponse) {
            return $clienteData; // Error en API
        }

        //Verificar si ya existe
        $errorExistente = self::verificarCliente($numeroCliente);
        if ($errorExistente) {
            return $errorExistente;
        }

        $clienteEmail= validarService::obtenerEmail($clienteData);
        if ($clienteEmail instanceof JsonResponse) {
            return $clienteEmail; // Error al obtener email
        }

        // retornar datos
        return [
            'numero' => $numeroCliente,
            'clienteData' => $clienteData,
            'email' => $clienteEmail
        ];
    }

    /*public static function obtenerDatosCliente($numeroCliente)
    {
        // Validar con API
        $clienteData = self::validarClienteAPI($numeroCliente, false);
        if ($clienteData instanceof JsonResponse) {
            return $clienteData; // Error en API
        }

        return [
            'numero' => $numeroCliente,
            'clienteData' => $clienteData
        ];
    }*/
}
