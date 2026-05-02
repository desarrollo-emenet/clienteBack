<?php

namespace App\Service\Invoice;

use Illuminate\Support\Facades\Http;

class ApiPagoraliaService
{
    public static function peticionAPIPagoralia(array $data)
    {
        //$web_key = env('API_PAGORALIA_WEB');
        $web_url = env('API_PAGORALIA');

        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            //'x-web-key' => $web_key
        ])->withoutVerifying()
            ->post($web_url, $data);

        if ($peticion->failed()) {
            return []; // Retornar null para indicar error en la petición
        }
        return $peticion->json();
    }
}
