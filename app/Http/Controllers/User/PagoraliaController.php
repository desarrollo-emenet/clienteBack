<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Service\Invoice\ApiPagoraliaService;
use App\Service\Invoice\InvoiceService;
use App\Service\servicios\validarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoraliaController extends Controller
{

    protected $validarService;

    public function __construct(validarService $validarService)
    {
        $this->validarService = $validarService;
    }

    //
    public function crearOrdenPagoralia(Request $request)
    {
        $request->validate([
            'cliente_n' => 'required|string'
        ]);
        $numero = $request->input('cliente_n');
        Log::info('Numero de cliente recibido para pago: ' . $numero);

        //obtener datos del cliente
        $datosCliente = $this->validarService->validarClienteAPI($numero);

        if ($datosCliente instanceof \Illuminate\Http\JsonResponse) {
            return $datosCliente;
        }

        $clienteData = $datosCliente;
        Log::info('Datos del cliente obtenidos para pago: ', $clienteData);

        //construir invoice
        $invoice = InvoiceService::construirInvoiceDesdeBilling($clienteData['cliente']['cliente']);

        //separar nombre y apellido
        $nombreApellido = InvoiceService::separarNombreApellido($clienteData['cliente']['nombre']);

        $data = [
            'isUnique' => 1,
            'invoice' => $invoice,
            'cliente' => $clienteData['cliente']['cliente'],
            'nombre' => $nombreApellido['nombre'],
            'apellido' => $nombreApellido['apellido'],
            'monto' => InvoiceService::formatearMontoPagoralia($clienteData['cliente']['deuda']),
            'moneda' => 'MXN'
        ];

        $peticion = ApiPagoraliaService::peticionAPIPagoralia($data);

        Log::info('Pagoralia Request', $data);
        Log::info('Pagoralia Response', $peticion);

        //validar peticion
        $redirectUrl = $peticion['data']['redirect_url'] ?? null;

        if (!$peticion || !$redirectUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden en Pagoralia'
            ], 500);
        }  

        return response()->json([
            'status' => true,
            'message' => 'Orden creada exitosamente en Pagoralia',
            'redirectUrl' => $redirectUrl
        ]);
    }
}
