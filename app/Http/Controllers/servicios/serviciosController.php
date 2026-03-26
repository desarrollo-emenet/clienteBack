<?php

namespace App\Http\Controllers\servicios;

use App\Http\Controllers\Controller;
use App\Mail\ServiceVerificationMail;
use App\Models\ServiceVerification;
use App\Service\clientService;
use App\Service\servicios\clientesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class serviciosController extends Controller
{
    protected $clientesService;
    protected $rules = ['numero_cliente'   => 'required|string|max:6|unique:services,numero_cliente'];

    public function __construct(clientesService $clientesService)
    {
        $this->clientesService = $clientesService;
    }

    public function index(Request $request)
    {
        try {
            return $this->clientesService->index($request);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Error al obtener datos de clientes. ' . $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_cliente'   => 'required|string|max:6|unique:services,numero_cliente'
        ], [], ['numero_cliente' => 'Número de cliente']);

        try {
            DB::beginTransaction();
            return $this->clientesService->store($request);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Error al obtener datos de clientes. ' . $th->getMessage(),
            ], 500);
        }


    }
}
