<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Service\Pagos\desencriptarInvoiceService;
use App\Service\Pagos\pagosService;
use App\Service\servicios\validarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoraliaController extends Controller
{

    protected $validarService;
    protected $pagosService;

    public function __construct(validarService $validarService, pagosService $pagosService)
    {
        $this->validarService = $validarService;
        $this->pagosService = $pagosService;
    }

    //
    public function crearOrdenPagoralia(Request $request)
    {
        $request->validate([
            'numero_cliente' => 'required|string'
        ]);

        $numero = $request->input('numero_cliente');

        try {
            //obtener datos del cliente
            $datosCliente = $this->validarService->validarClienteAPI($numero);
            if ($datosCliente instanceof \Illuminate\Http\JsonResponse) {
                return $datosCliente;
            }

            $clienteData = $datosCliente;

            //$monto = $clienteData['cliente']['deuda'];

            //si el cliente tiene deuda, se toma esa cantidad, sino se calcula el total mensual * 1 para adelantarse al pago del mes
            if ($clienteData['cliente']['deuda'] > 0) {
                $monto = floatval($clienteData['cliente']['deuda']);
            } else {
                $totalmensual = $this->pagosService->calcularTotalMensual($clienteData['servicios']);
                $monto = ($totalmensual) * 1;
            }

            //construir la info para pagoralia
            $data = $this->pagosService->construirDataPago($clienteData, $monto);

            //genera la orden en pagoralia y obtiene la url de redireccionamiento
            return $this->pagosService->generarOrdenPagoralia($data);
        } catch (\Exception $e) {
            Log::error('Error en crearOrdenPagoralia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear la orden de pago'
            ], 500);
        }
    }

    public function desencriptarInvoice(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string'
        ]);

        $invoiceRaw = $request->input('invoice');

        try {
            $resultado = desencriptarInvoiceService::desencriptarInvoice($invoiceRaw);
            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
