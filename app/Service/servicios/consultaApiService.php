<?php

namespace App\Service\servicios;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class consultaApiService
{
    public static function peticionAPI(string $numeroCliente, string $conexion)
    {
        $web_key = env('API_WEB');
        $web_url = env('API_URL');

        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            'x-web-key' => $web_key
        ])->withoutVerifying()
            ->get($web_url . $numeroCliente . '?conexion=' . $conexion);

        if ($peticion->failed()) {
            Log::error('Error en la petición a la API: ' . $peticion->body());
            return []; // Retornar null para indicar error en la petición
        }
        return $peticion->json();
    }

    /*public function obtenerCliente(
        string $numeroCliente,
        string $conexion,
        int $ttl = 5
    ) {
        $cacheKey = "clientes_api:{$numeroCliente}:{$conexion}";

        return Cache::remember($cacheKey, now()->addMinutes($ttl), function () use ($numeroCliente, $conexion) {

            $web_key = env('API_WEB');
            $web_url = env('API_URL');

            $peticion = Http::withHeaders([
                'Accept' => 'application/json',
                'x-web-key' => $web_key
            ])
                ->withoutVerifying()
                ->get($web_url . $numeroCliente . '?conexion=' . $conexion );

            if ($peticion->failed()) {
                throw new \RuntimeException('Error al consultar clientesV2');
            }

            return $peticion->json();
        });
    }*/
}
