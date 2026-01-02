<?php

namespace App\Service;

use Carbon\Carbon;


class UserService
{
    //
    public static function calcularAdeudo(array $servicios, ?Carbon $fecha = null): array
    {
        $fecha = $fecha ?: Carbon::now()->startOfMonth();

        $lastPaid = null;
        if (!empty($servicios['estadoCuenta']) && is_array($servicios['estadoCuenta'])) {
            foreach ($servicios['estadoCuenta'] as $item) {
                if (empty($item['fechaEmision'])) continue;
                try {
                    $d = Carbon::createFromFormat('d-m-y', $item['fechaEmision'])->startOfMonth();
                } catch (\Exception $e) {
                    try {
                        $d = Carbon::parse($item['fechaEmision'])->startOfMonth();
                    } catch (\Exception $e2) {
                        continue;
                    }
                }
                if ($lastPaid === null || $d->gt($lastPaid)) {
                    $lastPaid = $d;
                }
            }
        }

        if ($lastPaid === null) {
            // establecer "último pago" como null
            $lastPaid = null;
        }

        //importe a pagar
        $monthly = 0.0;
        if (!empty($servicios['internet']['precio'])) {
            //floatval() convierte un valor a número decimal (float).
            $monthly += floatval($servicios['internet']['precio']);
        }

        if (!empty($servicios['camaras']['precio']) && !empty($servicios['camaras']['canServicios'])) {
            //intval() convierte un valor a entero (integer).
            $monthly += floatval($servicios['camaras']['precio']) * intval($servicios['camaras']['canServicios']);
        }
        if (!empty($servicios['telefonia']['precio']) && isset($servicios['telefonia']['lineas'])) {
            $monthly += floatval($servicios['telefonia']['precio']) * intval($servicios['telefonia']['lineas']);
        }

        $owedMonths = [];
        $count = 0;
        $totalDue = 0.0;

        if ($lastPaid === null) {
            return [
                'lastPaid' => null,
                'owedMonths' => [],
                'count' => 0,
                'monthly_amount' => $monthly,
                'total_due' => 0.0,
            ];
        }

        $cursor = $lastPaid->copy()->addMonth()->startOfMonth();
        $end = $fecha->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            // YYYY-MM
            $key = $cursor->format('Y-m');
            $label = $cursor->isoFormat('M Y'); // "Oct 2025"
            $owedMonths[$key] = $label;
            $count++;
            $totalDue += $monthly;
            $cursor->addMonth();
        }

        return [
            //'lastPaid' => $lastPaid->format('Y-m'),
            //'owedMonths' => $owedMonths,
            //'count' => $count,
            //'monthly_amount' => $monthly,
            'total_due' => $totalDue,
        ];
    }
}
