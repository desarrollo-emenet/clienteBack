<?php

namespace App\Service\servicios;

use App\Mail\ServiceVerificationMail;
use App\Models\Service;
use App\Models\ServiceVerification;
use App\Service\clientService;
use App\Service\metadataService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class clientesService
{
    protected $validarService;
    protected $consultaApiService;
    protected $metadataService;

    public function __construct(validarService $validarService, consultaApiService $consultaApiService, metadataService $metadataService)
    {
        $this->validarService = $validarService;
        $this->consultaApiService = $consultaApiService;
        $this->metadataService = $metadataService;

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
            //$cliente = consultaApiService::peticionAPI((string) $servicio->numero_cliente, 'false');  -------------------------------
            $cliente = $this->metadataService->getMetadataForCliente((string) $servicio->numero_cliente,$user);
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


    public function destroy($id)
    {
        // Verificar que el servicio pertenece al usuario
        $service = Service::where('id', $id)->where('user_id', Auth::user()->id)
            ->firstOrFail();

        if (!$service) return response()->json([
            'status' => 'error',
            'errors' => 'No se encontró información del recurso solicitado'
        ], 404);

        // Verificar que el usuario tenga al menos un servicio antes de eliminar
        $totalServicios = Service::where('user_id', Auth::user()->id)->count();

        // Si solo tiene un servicio, no permitir eliminar
        if ($totalServicios <= 1) return response()->json([
            'status' => 'error',
            'message' => 'tu cuenta debe tener al menos un servicio'
        ], 409);

        //eliminar metadata asociada al numero_cliente
        $this->metadataService->eliminarMetadata(Auth::user(),$service->numero_cliente);

        // Eliminar el servicio
        $service->delete();        

        DB::commit();
        return response()->json(['message' => 'Servicio eliminado'], 200);
    }

    public function confirmarServicio($request)
    {
        $userId = $request->user()->id;
        //verificar que el codigo y numero_cliente coincidan con el registro de verificacion
        $verificacion = ServiceVerification::where('user_id', $userId)
            ->where('numero_cliente', $request->numero_cliente)
            ->where('codigo', $request->codigo)
            ->first();

        if (!$verificacion) return response()->json([
            "status" => "error",
            'message' => 'Código incorrecto.'
        ], 400);

        if ($verificacion->isExpired()) return response()->json([
            "status" => "error",
            'message' => 'El código de verificación ha expirado.'
        ], 400);

        //agregar el servicio al usuario y eliminar el registro de verificacion

        Service::create([
            'numero_cliente' => $verificacion->numero_cliente,
            'user_id' => $userId,
        ]);
        $verificacion->delete();
        DB::commit();
        return response()->json([
            "status" => "success",
            'message' => 'Servicio agregado correctamente'
        ], 201);
    }
}
