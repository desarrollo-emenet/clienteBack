<?php

namespace App\Service;

use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\codificacionService;

class clientService
{
    //CODIGOS DE CLIENTE - VALIDACIONES Y CONEXION A API EXTERNA
    // Función para obtener datos del cliente desde API
    public static function peticionAPI(string $numeroCliente, string $conexion)
    {
        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            'x-web-key' => 'web_9825f8agd35dfd4bg15fsd3a94c947a28896d5fd58gjh0f251a38912a'
        ])->withoutVerifying()
            ->get('https://isp-back.emenet.mx/api/clientesV2/' . $numeroCliente . '?conexion=' . $conexion);

        if ($peticion->failed()) {
            return null; // Retornar null para indicar error en la petición
        }
        return $peticion->json();
    }


    public static function obtenerCliente( $cliente)
    {
        //Descodificar número
        /*$cliente = codificacionService::descodificarClienteConLetra($numEncriptado);
        if ($cliente instanceof JsonResponse) {
            return $cliente; // Error en descodificación
        }*/
        // Realizar petición a la API externa
         $clienteData = self::peticionAPI($cliente, 'false');

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
        
        $clienteData = self::peticionAPI($numeroCliente, 'false');

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

        // retornar datos
        return [
            'numero' => $numeroCliente,
            'clienteData' => $clienteData
        ];
    }

    public static function obtenerDatosCliente($numeroCliente)
    {
        //decofificar numero de cliente
        /*$numeroCliente = codificacionService::descodificarClienteConLetra($numeroCliente);
        if ($numeroCliente instanceof JsonResponse) {
            return $numeroCliente; // Error en descodificación
        }*/

        // Validar con API 
        $clienteData = self::validarClienteAPI($numeroCliente, false);
        if ($clienteData instanceof JsonResponse) {
            return $clienteData; // Error en API
        }

        return [
            'numero' => $numeroCliente,
            'clienteData' => $clienteData
        ];
    }
}