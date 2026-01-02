<?php

namespace App\Service;

use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class clientService
{
    //CODIGOS DE CLIENTE - VALIDACIONES Y CONEXION A API EXTERNA
    //===================================================================
    // Función para obtener datos del cliente desde API
    public static function obtenerCliente( $numEncriptado)
    {
        //Descodificar número
        $cliente = self::descodificarClienteConLetra($numEncriptado);
        if ($cliente instanceof JsonResponse) {
            return $cliente; // Error en descodificación
        }
        // Realizar petición a la API externa
        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            'x-web-key' => 'web_9825f8agd35dfd4bg15fsd3a94c947a28896d5fd58gjh0f251a38912a'
        ])->withoutVerifying()
            ->get('https://dev.emenet.mx/api/clientesV2/' . $cliente);
        

        $clienteData = $peticion->json();

        return $clienteData;
    }

    //Validar numero de cliente con la API
    public static function validarClienteAPI(string $numeroCliente, bool $verificarBaja = true)
    {
        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            'x-web-key' => 'web_9825f8agd35dfd4bg15fsd3a94c947a28896d5fd58gjh0f251a38912a'
        ])->withoutVerifying()
            ->get('https://dev.emenet.mx/api/clientesV2/' . $numeroCliente);

        if ($peticion->failed()) {
            return response()->json([
                'message' => $peticion->status() === 404
                    ? 'Servicio no encontrado en la API externa'
                    : 'Error al obtener datos externos',
            ], $peticion->status() === 404 ? 404 : 422);
        }

        $clienteData = $peticion->json();

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
    public static function verificarCliente(string $numEncriptado): ?JsonResponse
    {
        if (Service::where('numero_cliente', $numEncriptado)->exists()) {
            return response()->json([
                'message' => 'El número de cliente ya está registrado en el sistema.'
            ], 409);
        }
        return null;
    }


    public static function validarClienteCompleto(string $numEncriptado)
    {
        //Descodificar número
        $numeroCliente = self::descodificarClienteConLetra($numEncriptado);
        if ($numeroCliente instanceof JsonResponse) {
            return $numeroCliente; // Error en descodificación
        }

        // Validar con API 
        $clienteData = self::validarClienteAPI($numeroCliente, true);
        if ($clienteData instanceof JsonResponse) {
            return $clienteData; // Error en API
        }

        //Verificar si ya existe
        $errorExistente = self::verificarCliente($numEncriptado);
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
        $numeroCliente = self::descodificarClienteConLetra($numeroCliente);
        if ($numeroCliente instanceof JsonResponse) {
            return $numeroCliente; // Error en descodificación
        }

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

    




    // Códigos de cliente - funciones de codificación y descodificación

   // Constantes para la codificación (deben coincidir EXACTAMENTE con JavaScript)
    private const PREFIJO_CODIGO = '00';
    private const MOD_CODIGO = 1000000; // 6 dígitos
    private const KEY_ENC = 1; // A=1 para mapeo lineal estable
    private const OFFSET_CODIGO = 334260; // B: offset de encriptación
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    // ✅ Inverso multiplicativo de 1 mod 1000000 es 1
    private const INV_ENC = 1;

    // ==========================================
    // FUNCIONES DE VALIDACIÓN
    // ==========================================

    /**
     * Valida si es un código encriptado de 8 dígitos (formato legado)
     */
    public static function esCodigoClienteEncriptado($s): bool
    {
        $t = trim((string)$s);
        return preg_match('/^\d{8}$/', $t) && str_starts_with($t, self::PREFIJO_CODIGO);
    }

    /**
     * Valida si es un código de cliente con letra (formato nuevo)
     */
    public static function esCodigoClienteConLetra($s): bool
    {
        $t = trim((string)$s);
        return (bool)preg_match('/^00\d{6}-[A-Z]$/i', $t);
    }

    /**
     * Normaliza solo dígitos
     */
    public static function normalizarSoloDigitos($s): string
    {
        return preg_replace('/\D+/', '', (string)$s);
    }

    // ==========================================
    // FUNCIONES DE ENCRIPTACIÓN
    // ==========================================

    /**
     * Codifica un número de cliente a formato base 8 dígitos ('00' + 6 codificados)
     * Fórmula: y = (A * n + B) mod M
     */
    public static function codificarClienteBase($numero): string
    {
        $limpio = self::normalizarSoloDigitos($numero);
        
        if (empty($limpio)) {
            throw new Exception('Ingresa un número válido');
        }

        $n = (int)$limpio;
        
        // Fórmula: y = (KEY_ENC * n + OFFSET_CODIGO) % MOD_CODIGO
        $y = ($n * self::KEY_ENC + self::OFFSET_CODIGO) % self::MOD_CODIGO;
        
        return self::PREFIJO_CODIGO . str_pad((string)$y, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Codifica cliente con letra de control (formato nuevo 9 caracteres)
     */
    public static function codificarClienteConLetra($numero): string
    {
        $base = self::codificarClienteBase($numero); // 8 dígitos
        $y = (int)substr($base, 2); // Obtener los 6 dígitos
        $letra = self::ALPHABET[$y % strlen(self::ALPHABET)];
        
        return $base . '-' . $letra;
    }

    // ==========================================
    // FUNCIONES DE DESENCRIPTACIÓN
    // ==========================================

    /**
     * Descodifica un código de cliente encriptado de 8 dígitos
     * Fórmula: n = INV_ENC * (y - OFFSET_CODIGO) mod MOD_CODIGO
     * 
     * @param string $codigo8 Código encriptado (8 dígitos, inicia con '00')
     * @param int|null $padLen Longitud fija para padding (null=auto 5/6)
     * @return string Número de cliente decodificado
     */
    public static function descodificarClienteBase(string $codigo8, ?int $padLen = null): string
    {
        $t = trim((string)$codigo8);

        // Validar formato básico
        if (!preg_match('/^00\d{6}$/', $t)) {
            return preg_replace('/\D+/', '', $t); // Fallback: solo dígitos
        }

        // Obtener los 6 dígitos (eliminar el prefijo '00')
        $y = (int)substr($t, 2);

        // Fórmula de desencriptación: n = INV_ENC * (y - OFFSET_CODIGO) mod MOD_CODIGO
        $adjusted = self::modulo($y - self::OFFSET_CODIGO, self::MOD_CODIGO);
        $n = self::modulo(self::INV_ENC * $adjusted, self::MOD_CODIGO);

        // Convertir a string
        $original = (string)$n;

        // Determinar ancho de padding: 5 si n < 10000, 6 si n >= 10000
        if ($padLen !== null && $padLen > 0) {
            $width = $padLen;
        } else {
            $width = ($n < 10000) ? 5 : 6;
        }

        return str_pad($original, $width, '0', STR_PAD_LEFT);
    }

    /**
     * Descodifica código con letra (valida letra de control)
     * 
     * @param string $codigo Código: '00' + 6 dígitos + guion + letra A-Z
     * @param int|null $padLen Longitud para padding (null=auto 5/6)
     * @return string Número original con padding
     * @throws Exception Si el formato o la letra son inválidos
     */
    public static function descodificarClienteConLetra(string $codigo, ?int $padLen = null): string
    {
        $t = strtoupper(trim($codigo));

        // Validar formato: 00 + 6 dígitos + guion + letra
        if (!preg_match('/^00(\d{6})-([A-Z])$/i', $t, $m)) {
            throw new Exception('Formato inválido (debe ser 00 + 6 dígitos + guion + letra)');
        }

        $digitos = (int)$m[1];
        $letra = $m[2];

        // Calcular letra esperada
        $letraEsperada = self::ALPHABET[$digitos % strlen(self::ALPHABET)];

        if ($letra !== $letraEsperada) {
            throw new Exception("Letra incorrecta. Se esperaba '{$letraEsperada}', pero se recibió '{$letra}'.");
        }

        // Desencriptar usando los 8 dígitos base: '00' + 6 dígitos
        $base8 = '00' . $m[1];

        return self::descodificarClienteBase($base8, $padLen);
    }

    /**
     * Normaliza una entrada de número de cliente
     * 
     * @param string $input Entrada del usuario
     * @param int|null $padLen Longitud de padding (null=auto 5/6)
     * @return string Número normalizado
     */
    public static function normalizarNumeroClienteEntrada(string $input, ?int $padLen = null): string
    {
        $rawUpper = strtoupper(trim($input));

        // Si es código con letra, intentar desencriptar
        if (self::esCodigoClienteConLetra($rawUpper)) {
            try {
                return self::descodificarClienteConLetra($rawUpper, $padLen);
            } catch (Exception $e) {
                Log::warning("Fallo al decodificar código con letra: " . $e->getMessage());
                // Fallback: solo dígitos
                return preg_replace('/\D+/', '', $rawUpper);
            }
        }

        // Si no es código con letra, retornar solo dígitos
        return preg_replace('/\D+/', '', $rawUpper);
    }

    // ==========================================
    // FUNCIÓN PRINCIPAL
    // ==========================================

    /**
     * Función calcula el resultado según el modo
     */
    public static function calcularResultado($valor, string $modo): array
    {
        try {
            $resultado = '';

            if ($modo === 'descodificar') {
                $raw = strtoupper(trim((string)$valor));
                // Normalizar formato con guion si es necesario
                $raw = preg_replace('/^(00\d{6})-?([A-Z])$/', '$1-$2', $raw);

                if (self::esCodigoClienteConLetra($raw)) {
                    $resultado = self::descodificarClienteConLetra($raw);
                } elseif (self::esCodigoClienteEncriptado($raw)) {
                    $resultado = self::descodificarClienteBase($raw) . ' (legado)';
                } else {
                    throw new Exception('Usa 00 + 6 dígitos + guion + letra (ej. 00123456-G) o el formato legado 00123456');
                }
            } else {
                $resultado = self::codificarClienteConLetra($valor);
            }

            return [
                'success' => true,
                'resultado' => $resultado,
                'error' => null,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'resultado' => null,
                'error' => $e->getMessage() ?: 'Error al procesar el código',
            ];
        }
    }

    // ==========================================
    // FUNCIONES AUXILIARES
    // ==========================================

    /**
     * Operación módulo segura (siempre retorna positivo)
     * 
     * @param int $a Dividendo
     * @param int $m Módulo
     * @return int Resultado positivo
     */
    private static function modulo(int $a, int $m): int
    {
        $result = $a % $m;
        return $result < 0 ? $result + $m : $result;
    }
}
