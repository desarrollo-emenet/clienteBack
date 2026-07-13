<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use App\Service\Auth\verifyMailServices;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class VerifyMailController extends Controller
{

 private $verifyMailServices;

    public function __construct(verifyMailServices $verifyMailServices)
    {
        $this->verifyMailServices = $verifyMailServices;
    }
    

    public function verify(Request $request, string $id, string $hash)
    {
        try {
            Log::info('hola');
            return $this->verifyMailServices->verify($request, $id, $hash);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Ocurrió un error al verificar el correo. ' . $th->getMessage(),
            ], 500);
        }
    }

    public function validarToken(Request $request)
    {
        try {
            return $this->verifyMailServices->validarToken($request);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Ocurrió un error al validar el token. ' . $th->getMessage(),
            ], 500);
        }
    }
}
