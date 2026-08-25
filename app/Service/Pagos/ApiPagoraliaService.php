<?php

namespace App\Service\Pagos;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class ApiPagoraliaService
{
    public static function peticionAPIPagoralia($request)
    {
        $web_url = env('API_PAGORALIA');
        $token = env("TOKEN_PAGORALIA");
        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->withoutVerifying()
            ->post($web_url, $request);

        if ($peticion->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se genero la referencia de pago.',
                "error" => $peticion->body(),
            ], 500);
        }
        return $peticion->json();
    }
}
