<?php

namespace App\Http\Controllers;

use App\Service\clientService;
use App\Models\Service;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    //
    public function __construct()
    {
        //
        $this->middleware('auth:sanctum');
    }
    public static $rules = [
        'numero_cliente'   => 'required|string|max:10|unique:services,numero_cliente',

    ];


    public function index(Request $request)
    {
        $user = $request->user();
        $servicios = $user->servicios()->orderBy('id', 'desc')->get();

        $clientesData = [];
        try {
            foreach ($servicios as $servicio) {
                $clientesData[$servicio->id] = clientService::obtenerCliente(
                    (string) $servicio->numero_cliente
                );
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de clientes: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener datos de clientes',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(
            [
                'servicios' => $servicios,
                'cliente' => $clientesData
            ],
            200
        );
    }

    public function AddService(Request $request)
    {

        $data = $request->validate(self::$rules);

        $validacion = clientService::validarClienteCompleto($data['numero_cliente']);

        if ($validacion instanceof \Illuminate\Http\JsonResponse) {
            return $validacion; // Retornar error si hubo problema en validación
        }

        // Extraer datos validados
        $numEncriptado = $data['numero_cliente'];
        $numeroCliente = $validacion['numero'];
        $clienteData = $validacion['clienteData'];

        // Crear dentro de transacción y asignar user_id (del usuario autenticado)
        $userId = $request->user()->id; // requiere auth
        $servicio = null;


        try {
            DB::transaction(function () use (&$servicio, $userId, $numEncriptado) {
                $servicio = Service::create([
                    'numero_cliente' => $numEncriptado, //guardar numero encriptado
                    'user_id' => $userId,
                ]);
            });

            return response()->json([
                'mensaje' => 'Registro Creado',
                'data'    => $servicio,
                'cliente' => $clienteData,
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al crear el servicio',
                'error' => $e->getMessage(),
            ], 422);
        }
    }


    public function verificarAcceso(Request $request, $numero)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'has_access' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $servicio = Service::where('numero_cliente', $numero)
            ->where('user_id', $user->id)
            ->first();

        if ($servicio) {
            return response()->json([
                'has_access' => true,
                'servicio' => [
                    'id' => $servicio->id,
                    'numero_cliente' => $servicio->numero_cliente,
                    'user_id' => $servicio->user_id
                ]
            ], 200);
        }

        return response()->json([
            'has_access' => false,
            'message' => 'No tienes acceso a este servicio'
        ], 403);
    }

    public function destroy(Request $request, $id)
    {
        //
        $user = $request->user();

        $service = Service::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $totalServicios = Service::where('user_id', $user->id)->count();

        if ($totalServicios <= 1) {
            return response()->json([
                'message' => 'tu cuenta debe tener al menos un servicio'
            ], 409);
        }

        $service->delete();
        return response()->json(['message' => 'Servicio eliminado'], 200);
    }
}
