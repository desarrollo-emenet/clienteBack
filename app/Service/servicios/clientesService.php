<?php

namespace App\Service\servicios;

use App\Mail\ServiceVerificationMail;
use App\Models\ServiceVerification;
use App\Service\clientService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class clientesService
{
    protected $validarService;
    protected $consultaApiService;

    public function __construct(validarService $validarService, consultaApiService $consultaApiService)
    {
        $this->validarService = $validarService;
        $this->consultaApiService = $consultaApiService;
    }

    public function index($request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();
        $servicios = $user->servicios()->orderBy('id', 'desc')->get();

        // Obtener datos de clientes para cada servicio
        $clientesData = [];
        foreach ($servicios as $servicio) {
            // Obtener datos del cliente usando el número de cliente encriptado
            $cliente = consultaApiService::peticionAPI((string) $servicio->numero_cliente, 'false');
            if ($cliente) {
                $cliente['idServicio'] = $servicio->id;
                $clientesData[] = $cliente;
            }
        }

        return response()->json([
            'servicios' => $clientesData,
        ], 200);
    }

    public function store($request)
    {
        $validacion = $this->validarService->validarClienteCompleto($request->numero_cliente);
        if ($validacion instanceof JsonResponse) return $validacion; // Retornar error si hubo problema en validación

        // Extraer datos validados
        $numeroCliente = $validacion['cliente'];
        $email = $validacion['email'];
        $userId = $request->user()->id;
        $codigo = rand(100000, 999999); // Generar código de verificación aleatorio


        //si hay registro anterior se elimina
        ServiceVerification::where('user_id', $userId)
            ->where('numero_cliente', $numeroCliente)
            ->delete();

        // Crear el registro de verificación asociado
        ServiceVerification::create([
            'numero_cliente' => $numeroCliente,
            'codigo' => $codigo,
            'expires_at' => now()->addMinutes(10), //expira el codigo en 10 minutos
            'user_id' => $userId,
        ]);


        try {
            Mail::to($email)->send(new ServiceVerificationMail($codigo));
            DB::commit();
            return response()->json([
                "status" => "succecss",
                'message' => 'Código de verificación enviado correctamente.',
            ], 201);
        } catch (TransportException $e) {
            Log::info("Fallo en la conexión SMTP: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "mensaje" => "No se realizo el envio del correo de verificación "
            ], 500);
        }
    }
}
