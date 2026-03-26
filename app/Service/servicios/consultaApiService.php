<?php

namespace App\Service\servicios;

use Illuminate\Support\Facades\Http;

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
            return []; // Retornar null para indicar error en la petición
        }
        return $peticion->json();
    }
}
