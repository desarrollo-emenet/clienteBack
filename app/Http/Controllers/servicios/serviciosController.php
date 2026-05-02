<?php

namespace App\Http\Controllers\servicios;

use App\Http\Controllers\Controller;
use App\Service\servicios\clientesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            return $this->clientesService->destroy($id);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Error al eliminar el servicio. ' . $th->getMessage(),
            ], 500);
        }
    }

    public function confirmarServicio(Request $request)
    {
        $request->validate([
            'numero_cliente' => 'required|string|max:6',
            'codigo' => 'required|digits:6',
        ]);

        try {
            DB::beginTransaction();
            return $this->clientesService->confirmarServicio($request);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Error al confirmar. ' . $th->getMessage(),
            ], 500);
        }
    }
}
