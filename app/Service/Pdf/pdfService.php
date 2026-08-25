<?php

namespace App\Service\Pdf;

use App\Service\User\metadataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class pdfService
{

    protected $metadataService;
    public function __construct(metadataService $metadataService)
    {
        $this->metadataService = $metadataService;
    }
    public function informeTrimestral(String $numero)
    {
        $user = Auth::user();

        $datosCliente = $this->metadataService
            ->getMetadataForCliente($numero, $user);

        $datosInforme = $this->prepararDatosInforme($datosCliente);

        $pdf = Pdf::loadView('pdf.informe', $datosInforme);

        $pdfIn = $pdf->output();

        return response($pdfIn, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="informe.pdf"',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }


    public function prepararDatosInforme(array $datosCliente): array
    {
        $cliente = $datosCliente['cliente'] ?? [];
        $servicios = $datosCliente['servicios'] ?? [];

        //cliente
        $numeroCliente = $cliente['cliente'] ?? 'N/A';
        $nombreCliente = ucwords(strtolower($cliente['nombre'] ?? 'N/A'));
        
        $direccion = ucwords(strtolower($cliente['direccion'] ?? ''));
        $colonia = ucwords(strtolower($cliente['colonia'] ?? ''));
        $municipio = ucwords(strtolower($cliente['municipio'] ?? ''));
        $estado = ucwords(strtolower($cliente['estado'] ?? ''));

        $correo = $cliente['correo'] ?? '';

        //servicios
        $serviciosContratados = [];

        if (!empty($servicios['internet'])) {
            $precioInternet = (float) ($servicios['internet']['precio'] ?? 0);
            if ($precioInternet > 0) {
                $serviciosContratados[] = [
                    'tipo' => 'Internet',
                    'detalle' => $cliente['nombrePlan'] ?? 'Servicio de Internet',
                    'cantidad' => 1,
                    'precio' => $precioInternet,
                ];
            }
        }
        if (!empty($servicios['camaras'])) {

            $cantidadCamaras = (int) ($servicios['camaras']['canServicios'] ?? 0);
            $precioCamara = (float) ($servicios['camaras']['precio'] ?? 0);

            if ($cantidadCamaras > 0 && $precioCamara > 0) {
                $serviciosContratados[] = [
                    'tipo' => 'Cámaras',
                    'detalle' => $cantidadCamaras . ' cámara(s)',
                    'cantidad' => $cantidadCamaras,
                    'precio' => $precioCamara,
                ];
            }
        }

        if (!empty($servicios['telefono'])) {

            $cantidadTelefonos = (int) ($servicios['telefono']['canServicios'] ?? 0);
            $precioTelefonos = (float) ($servicios['telefono']['precio'] ?? 0);

            if ($cantidadTelefonos > 0 && $precioTelefonos > 0) {
                $serviciosContratados[] = [
                    'tipo' => 'Telefono',
                    'detalle' => $cantidadTelefonos . ' telefono(s)',
                    'cantidad' => $cantidadTelefonos,
                    'precio' => $precioTelefonos,
                ];
            }
        }

        if (!empty($servicios['cuentasTv'])) {

            $cantidadCuentasTv = (int) ($servicios['cuentasTv']['canServicios'] ?? 0);
            $precioCuentasTv = (float) ($servicios['cuentasTv']['precio'] ?? 0);

            if ($cantidadCuentasTv > 0 && $precioCuentasTv > 0) {
                $serviciosContratados[] = [
                    'tipo' => 'CuentasTv',
                    'detalle' => $cantidadCuentasTv . ' cuenta(s)',
                    'cantidad' => $cantidadCuentasTv,
                    'precio' => $precioCuentasTv,
                ];
            }
        }

        $cantidadServicios = count($serviciosContratados);

        $totalServicios = collect($serviciosContratados)
            ->sum(function ($servicio) {
                return ($servicio['cantidad'] ?? 0) * ($servicio['precio'] ?? 0);
            });


        //estado de cuenta 3 meses
        $estadoCuenta = $servicios['estadoCuenta'] ?? [];
        $ultimosTres = collect($estadoCuenta)
            ->take(-3)
            ->values();
        $totalTrimestral = $ultimosTres->sum(function ($item) {
            return (float) ($item['importe'] ?? 0);
        });

        if ($ultimosTres->count() > 0) {
            $primerPeriodo = $ultimosTres->first()['mensualidad'] ?? '';
            $ultimoPeriodo = $ultimosTres->last()['mensualidad'] ?? '';
            $periodo = $primerPeriodo;
            if ($primerPeriodo !== $ultimoPeriodo) {
                $periodo .= ' - ' . $ultimoPeriodo;
            }
        } else {
            $periodo = 'Sin información';
        }

        //deuda
        $deuda = (float) ($cliente['deuda'] ?? 0);
        $tieneDeuda = $deuda > 0;

        $mesesAdeudo = 0;
        if ($totalServicios > 0 && $deuda > 0) {
            $mesesAdeudo = (int) ceil(
                $deuda / $totalServicios
            );
        }

        $fechaEmision = now()->format('d/m/Y');

        return [
            'numeroCliente' => $numeroCliente,
            'nombreCliente' => $nombreCliente,

            'direccion' => $direccion,
            'colonia' => $colonia,
            'municipio' => $municipio,
            'estado' => $estado,

            'correo' => $correo,

            'serviciosContratados' => $serviciosContratados,
            'cantidadServicios' => $cantidadServicios,
            'totalServicios' => $totalServicios,

            'ultimosTres' => $ultimosTres,
            'totalTrimestral' => $totalTrimestral,

            'deuda' => $deuda,
            'tieneDeuda' => $tieneDeuda,
            'mesesAdeudo' => $mesesAdeudo,

            'periodo' => $periodo,
            'fechaEmision' => $fechaEmision,
        ];
    }
}
