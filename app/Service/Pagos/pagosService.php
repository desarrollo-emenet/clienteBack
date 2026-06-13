<?php

namespace App\Service\Pagos;

class pagosService
{

    public function construirDataPago(array $clienteData, float $monto)
    {
        // Construir el invoice con los datos del cliente
        $invoice = InvoiceService::construirInvoiceDesdeBilling(
            $clienteData['cliente']['cliente']
        );
        // Separar el nombre completo en nombre y apellido
        $nombreApellido = InvoiceService::separarNombreApellido(
            $clienteData['cliente']['nombre']
        );

        // Devolver la información necesaria para crear la orden de pago
        return [
            'isUnique' => 1,
            'invoice' => $invoice,
            'cliente' => $clienteData['cliente']['cliente'],
            'nombre' => $nombreApellido['nombre'],
            'apellido' => $nombreApellido['apellido'],
            'monto' => InvoiceService::formatearMontoPagoralia($monto),
            'moneda' => 'MXN'
        ];
    }

    public function generarOrdenPagoralia(array $data)
    {
        // Realizar la petición a Pagoralia
        $peticion = ApiPagoraliaService::peticionAPIPagoralia($data);

        // Verificar la respuesta de Pagoralia
        $redirectUrl = $peticion['data']['redirect_url'] ?? null;

        // Si no se obtuvo una URL de redireccionamiento, retornar un error
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


    public function calcularTotalMensual(array $servicios): float
    {

        if (!$servicios) return 0;
        $total = 0;

        //internet
        if (isset($servicios['internet']['precio'])) {
            $total += floatval($servicios['internet']['precio']);
        }

        //camaras
        if (isset($servicios['camaras']['precio']) && isset($servicios['camaras']['canServicios'])) {
            $precio = floatval($servicios['camaras']['precio']);
            $noCamaras = floatval($servicios['camaras']['canServicios']);
            $total += $precio * $noCamaras;
        }

        //telefonia
        if (isset($servicios['telefono']['precio']) && isset($servicios['telefono']['canServicios'])) {
            $precio = floatval($servicios['telefono']['precio']);
            $noTelefonos = floatval($servicios['telefono']['canServicios']);
            $total += $precio * $noTelefonos;
        }

        //tv
        if (isset($servicios['cuentasTv']['precio']) && isset($servicios['cuentasTv']['canServicios'])) {
            $precio = floatval($servicios['cuentasTv']['precio']);
            $noCuentas = floatval($servicios['cuentasTv']['canServicios']);
            $total += $precio * $noCuentas;
        }
        return $total;
    }
}
