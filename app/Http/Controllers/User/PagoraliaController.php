<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\pagoralia\pagoRequest;
use App\Service\Pagos\ApiPagoraliaService;
use App\Service\servicios\validarService;
use Throwable;

class PagoraliaController extends Controller
{

    protected $validarService;

    public function __construct(validarService $validarService)
    {
        $this->validarService = $validarService;
    }

    public function crearOrdenPagoralia(pagoRequest $request){
        try {
            return ApiPagoraliaService::peticionAPIPagoralia($request);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al iniciar sesión. ',
                "error" => $th->getMessage(),
            ], 500);
        }
    }
}
