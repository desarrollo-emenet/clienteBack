<?php

namespace App\Service\Pagos;

use Illuminate\Support\Facades\Http;

class ApiPagosService
{
    /*public static function peticionAPIPagos(array $data)
    {
        //$web_key = env('API_PAGOS_WEB');
        $web_url = env('API_PAGOS');

        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            //'x-web-key' => $web_key
        ])->withoutVerifying()
        
            ->post($web_url, $data);

        if ($peticion->failed()) {
            return []; // Retornar null para indicar error en la petición
        }
        return $peticion->json();
    }*/

    public static function peticionAPIPagos($request, array $data)
    {
        $web_url = env('API_PAGOS');

        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withoutVerifying()
            ->attach(
                'comprobante',
                file_get_contents($request->file('comprobante')->getRealPath()),
                $request->file('comprobante')->getClientOriginalName(),
                ['Content-Type' => $request->file('comprobante')->getMimeType()] //detectar tipo de dato
            )
            ->post($web_url, $data);

        if ($peticion->failed()) {
            return []; // Retornar null para indicar error en la petición
        }
        return $peticion->json();
    }
}
