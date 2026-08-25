<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe trimestral</title>

    <style>
        @page {
            margin: 25px 30px 48px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #252525;
            background: #fff;
            font-size: 9px;
            line-height: 1.25;
        }

        :root {
            --azul: #0b4e94;
            --azul-oscuro: #083d74;
            --azul-suave: #eef5fb;
            --gris-texto: #444;
            --gris: #777;
            --gris-claro: #f3f4f5;
            --borde: #d5dadd;
            --verde: #168c4b;
            --verde-fondo: #f3faf5;
            --rojo: #c62828;
            --rojo-fondo: #fff5f5;
        }

        .header {
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ccd2d7;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .logo {
            width: 58%;
            vertical-align: middle;
        }

        .logo img {
            display: block;
            width: 125px;
            height: auto;
        }

        .header-info {
            width: 42%;
            vertical-align: middle;
            text-align: right;
        }

        .header-title {
            margin-bottom: 3px;
            color: var(--azul);
            font-size: 17px;
            font-weight: bold;
            line-height: 1.15;
        }

        .header-detail {
            color: #666;
            font-size: 9px;
            line-height: 1.45;
        }

        .header-detail strong {
            color: #222;
        }

        .client-box {
            width: 100%;
            margin-bottom: 10px;
            padding: 9px 11px;
            border: 1px solid var(--borde);
            border-left: 4px solid var(--azul);
            background: #fff;
        }

        .client-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .client-left {
            width: 67%;
            padding-right: 14px;
            vertical-align: top;
        }

        .client-right {
            width: 33%;
            padding-left: 14px;
            border-left: 1px solid #dfe3e6;
            vertical-align: top;
        }

        .client-label {
            color: #777;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .15px;
        }

        .client-name {
            display: inline;
            margin-left: 4px;

            color: #222;
            font-size: 10px;
            font-weight: bold;
        }

        .client-value {
            color: #333;
            font-size: 9px;
        }

        .client-value cliente {
            display: inline;
            margin-left: 4px;

            color: #222;
            font-size: 10px;
            font-weight: bold;
        }

        .client-left>.client-label {
            display: inline;
        }

        .client-left .client-block {
            margin-top: 5px;
        }

        .client-left .client-block:first-of-type {
            display: inline-block;
            margin-left: 12px;
            padding: 3px 7px;
            background: var(--azul-suave);
            border: 1px solid #c7d8e8;
            border-radius: 3px;
        }

        .client-left .client-block:first-of-type .client-label {
            color: var(--azul);
            font-size: 7px;
        }

        .client-left .client-block:first-of-type .client-value {
            color: var(--azul-oscuro);
            font-size: 10px;
            font-weight: bold;
        }

        .client-left .client-block:nth-of-type(2),
        .client-left .client-block:nth-of-type(3) {
            margin-top: 5px;
        }

        .client-left .client-block:nth-of-type(2) .client-label,
        .client-left .client-block:nth-of-type(3) .client-label {
            margin-right: 4px;
        }

        .client-left .client-block:nth-of-type(2) .client-label,
        .client-left .client-block:nth-of-type(2) .client-value,
        .client-left .client-block:nth-of-type(3) .client-label,
        .client-left .client-block:nth-of-type(3) .client-value {
            display: inline;
        }

        .client-right .client-block {
            padding-bottom: 5px;
            margin-bottom: 5px;
            border-bottom: 1px solid #e4e6e8;
        }

        .client-right .client-block:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .client-right .client-label {
            display: block;
            margin-bottom: 2px;
        }

        .client-right .client-value {
            color: var(--azul-oscuro);
            font-size: 10px;
            font-weight: bold;
        }

        .section {
            margin-top: 9px;
            margin-bottom: 9px;
        }

        .section-title {
            width: 100%;
            margin-bottom: 5px;
            padding: 4px 7px;
            color: var(--azul);
            background: var(--azul-suave);
            border-left: 3px solid var(--azul);
            border-bottom: 1px solid #d4e0e9;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .services-table,
        .history-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid var(--borde);
        }

        .services-table thead th,
        .history-table thead th {
            height: 23px;
            padding: 4px 6px;
            background: #f0f2f4;
            color: #505050;
            border-bottom: 1px solid #cfd4d8;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            vertical-align: middle;
            line-height: 1.1;
            white-space: nowrap;
        }

        .services-table tbody td,
        .history-table tbody td {
            height: 25px;
            padding: 4px 6px;
            color: #333;
            border-bottom: 1px solid #e3e5e7;
            font-size: 8.5px;
            vertical-align: middle;
            line-height: 1.2;
        }

        .services-table tbody tr:last-child td,
        .history-table tbody tr:last-child td {
            border-bottom: none;
        }

        .services-table th:nth-child(1),
        .services-table td:nth-child(1) {
            text-align: left;
        }

        .services-table th:nth-child(2),
        .services-table td:nth-child(2) {
            text-align: left;
        }

        .services-table th:nth-child(3),
        .services-table td:nth-child(3) {
            text-align: right;
        }

        .history-table th:nth-child(1),
        .history-table td:nth-child(1) {
            text-align: left;
        }

        .history-table th:nth-child(2),
        .history-table td:nth-child(2) {
            text-align: left;
        }

        .history-table th:nth-child(3),
        .history-table td:nth-child(3) {
            text-align: left;
        }

        .history-table th:nth-child(4),
        .history-table td:nth-child(4) {
            text-align: right;
        }

        .service-total td,
        .history-total td {
            height: 25px;
            background: #f1f3f5;
            border-top: 1px solid #cbd1d6;
            color: #222;
            font-weight: bold;
        }

        .service-total td:last-child,
        .history-total td:last-child {
            color: var(--azul-oscuro);
            font-size: 9px;
        }

        .service-price,
        .amount {
            text-align: right !important;
            white-space: nowrap;
            color: #222;
            font-weight: bold;
        }

        .status-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .debt-warning {
            width: 97%;
            padding: 8px 10px;
            background: var(--rojo-fondo);
            border: 1px solid #e0a9a9;
            border-left: 4px solid var(--rojo);
            border-radius: 6px;
        }

        .debt-title {
            margin-bottom: 2px;
            color: var(--rojo);
            font-size: 8.5px;
            font-weight: bold;
        }

        .debt-text {
            color: #666;
            font-size: 8px;
            line-height: 1.3;
        }

        .debt-text strong {
            color: #333;
        }

        .no-debt {
            width: 97%;
            padding: 8px 10px;
            background: var(--verde-fondo);
            border: 1px solid #acd2bb;
            border-left: 4px solid var(--verde);
            border-radius: 8px;
        }

        .no-debt-title {
            margin-bottom: 2px;
            color: var(--verde);
            font-size: 8.5px;
            font-weight: bold;
        }

        .no-debt-text {
            color: #666;
            font-size: 8px;
        }

        .status-value {
            margin-top: 1px;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.1;
        }

        .status-value.deuda {
            color: var(--rojo);
        }

        .status-value.correcto {
            color: var(--verde);
        }


        .payment-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .payment-cell {
            width: 50%;
            padding: 0 3px;
            vertical-align: top;
        }

        .payment-box {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d5dadd;
            background: #fff;
        }

        .payment-header {
            height: 23px;
            padding: 5px 7px;
            background: #f0f2f4;
            border-bottom: 1px solid #d4d8dc;
            color: #333;
            font-size: 8px;
            font-weight: bold;
            vertical-align: middle;
        }

        .payment-image {
            width: 30%;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        .payment-info {
            width: 70%;
            padding: 6px 7px;
            vertical-align: middle;
        }

        .payment-logo {
            display: block;
            width: auto;
            max-width: 55px;
            height: auto;
            margin: 0 auto;
        }

        .payment-barcode {
            display: block;
            max width: 55px;
            margin 5 auto;

        }

        .barcode {
            display: block;
            width: auto;
            max-width: 200px;
            height: auto;
            margin: 0 auto;

        }

        .payment-body {
            padding: 0;
            color: #444;
            font-size: 7.5px;
            line-height: 1.45;
        }

        .payment-label {
            color: #777;
        }

        .payment-value {
            color: #222;
            font-weight: bold;
        }


        .oxxo-box {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            border: 1px solid #d5dadd;
            background: #fff;
        }

        .oxxo-header {
            height: 23px;
            padding: 5px 7px;
            background: #f0f2f4;
            border-bottom: 1px solid #d4d8dc;
            color: #333;
            font-size: 8px;
            font-weight: bold;
            vertical-align: middle;
        }

        .oxxo-image {
            width: 25%;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        .oxxo-info {
            width: 75%;
            padding: 6px 7px;
            vertical-align: middle;
        }

        .oxxo-logo {
            display: block;
            width: auto;
            width: 180px;
            height: auto;
            margin: 0 auto;
        }

        .oxxo-body {
            padding: 0;
            color: #444;
            font-size: 7.5px;
            line-height: 1.45;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -35px;
            padding-top: 5px;
            border-top: 1px solid #d0d4d7;
            color: #999;
            font-size: 6.5px;
            text-align: center;
        }

        .footer strong {
            color: #666;
        }


        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .muted {
            color: #888 !important;
        }

        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        //variables
        $cliente = $cliente ?? [];
        $servicios = $servicios ?? [];
        $estadoCuenta = $servicios['estadoCuenta'] ?? [];

        //datos cliente
        $numeroCliente = $cliente['cliente'] ?? 'N/A';
        $nombreCliente = $cliente['nombre'] ?? 'N/A';
        $direccion = $cliente['direccion'] ?? '';
        $colonia = $cliente['colonia'] ?? '';
        $municipio = $cliente['municipio'] ?? '';
        $estado = $cliente['estado'] ?? '';
        $correo = $cliente['correo'] ?? '';
        $telefono = $cliente['telefono'] ?? '';
        $plan = $cliente['nombrePlan'] ?? '';

        //datos servicios
        $internet = $servicios['internet'] ?? [];
        $camaras = $servicios['camaras'] ?? [];
        $telefonoServicio = $servicios['telefono'] ?? [];
        $cuentaTv = $servicios['cuentaTv'] ?? [];

        //servicios activo
        $serviciosContratados = [];
        if (!empty($internet)) {
            $serviciosContratados[] = [
                'tipo' => 'Internet',
                'detalle' => $plan ?? 'Plan de Internet',
                'cantidad' => 1,
                'precio' => (float) ($internet['precio'] ?? 0),
            ];
        }

        $cantidadCamaras = (int) ($camaras['canServicios'] ?? 0);
        if ($cantidadCamaras > 0) {
            $precioCamara = (float) ($camaras['precio'] ?? 0);
            $serviciosContratados[] = [
                'tipo' => 'Cámaras',
                'detalle' => $cantidadCamaras . ' servicio(s)',
                'cantidad' => $cantidadCamaras,
                'precio' => $precioCamara,
            ];
        }

        $cantidadTelefono = (int) ($telefonoServicio['canServicios'] ?? 0);
        if ($cantidadTelefono > 0) {
            $precioTelefono = (float) ($telefonoServicio['precio'] ?? 0);
            $serviciosContratados[] = [
                'tipo' => 'Telefonía',
                'detalle' => $cantidadTelefono . ' línea(s)',
                'cantidad' => $cantidadTelefono,
                'precio' => $precioTelefono,
            ];
        }

        $cantidadTv = (int) ($cuentasTv['canServicios'] ?? 0);
        if ($cantidadTv > 0) {
            $precioTv = (float) ($cuentasTv['precio'] ?? 0);
            $serviciosContratados[] = [
                'tipo' => 'TV',
                'detalle' => $cantidadTv . ' servicio(s)',
                'cantidad' => $cantidadTv,
                'precio' => $precioTv,
            ];
        }

        //total mensual
        $totalServicios = collect($serviciosContratados)->sum(function ($servicio) {
            return $servicio['cantidad'] * $servicio['precio'];
        });

        //servicios contratados total
        $cantidadServicios = count($serviciosContratados);

        //ultimos 3 meses
        $ultimosTres = collect($estadoCuenta)
            ->sortByDesc(function ($item) {
                try {
                    return \Carbon\Carbon::createFromFormat(
                        'd-m-Y',
                        $item['fechaEmision'] ?? '01-01-1900'
                    )->timestamp;
                } catch (\Throwable $e) {
                    return 0;
                }
            })
            ->take(3)->reverse()->values();

        //deuda
        $deuda = (float) ($cliente['deuda'] ?? 0);
        $tieneDeuda = $deuda > 0;

        //total trimestral
        $totalTrimestral = $ultimosTres->sum(function ($item) {
            return (float) ($item['importe'] ?? 0);
        });

        //meses con adeudo
        $mesesAdeudo = 0;
        if ($totalServicios > 0 && $deuda > 0) {
            $division = $deuda / $totalServicios;
            if (floor($division) == $division) {
                $mesesAdeudo = (int) $division;
            }
        }

        //fecha
        $fechaEmision = now()->format('d/m/Y');

        //periodo
        $primerMes = $ultimosTres->first()['mensualidad'] ?? null;
        $ultimoMes = $ultimosTres->last()['mensualidad'] ?? null;
        $periodo = 'N/A';
        if ($primerMes && $ultimoMes) {
            $periodo = $primerMes;
            if ($primerMes !== $ultimoMes) {
                $periodo .= ' - ' . $ultimoMes;
            }
        }
    @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="http://localhost:4200/assets/img/emenetLogo.png">
                </td>
                <td class="header-info">
                    <div class="header-title">Informe trimestral</div>
                    <div class="header-detail">
                        Cliente:<strong>{{ $numeroCliente }}</strong>
                        <br>
                        Periodo:{{ $periodo }}
                        <br>Emitido:{{ $fechaEmision }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="client-box avoid-break">
        <table class="client-table">
            <tr>
                <td class="client-left">
                    <div class="client-label">Cliente</div>
                    <div class="client-name">{{ ucwords(strtolower($nombreCliente)) }}</div>
                    <div class="client-block">
                        <span class="client-label">Número de cliente:</span>
                        <span class="client-value cliente"><strong>{{ $numeroCliente }}</strong></span>
                    </div>

                    <div class="client-block">
                        <span class="client-label">Dirección:</span>
                        <span class="client-value">{{ ucwords(strtolower($direccion)) }}
                            @if(ucwords(strtolower(($colonia)))), {{ ucwords(strtolower(($colonia))) }}@endif
                            @if(ucwords(strtolower(($municipio)))), {{ ucwords(strtolower(($municipio))) }}@endif
                            @if(ucwords(strtolower(($estado)))), {{ ucwords(strtolower(($estado))) }}@endif
                        </span>
                    </div>

                    @if($correo)
                        <div class="client-block">
                            <span class="client-label">Correo:</span>
                            <span class="client-value">{{ $correo }}</span>
                        </div>
                    @endif
                </td>

                <td class="payment-barcode">
                    <img src="http://localhost:4200/assets/img/FormasPago/barcode.webp" class="barcode">
                </td>

                <!--<td class="client-right">
                    <div class="client-block">
                        <div class="client-label">Mensualidad</div>
                        <div class="client-value">${{ number_format($totalServicios, 2) }}</div>
                    </div>

                    <div class="client-block">
                        <div class="client-label">Periodo del informe</div>
                        <div class="client-value">{{ $periodo }}</div>
                    </div>
                    
                </td>-->
            </tr>
        </table>
    </div>

    <div class="section avoid-break">
        <div class="section-title">Detalle de servicios</div>
        <table class="services-table">
            <thead>
                <tr>
                    <th width="25%">Servicio</th>
                    <th width="50%">Detalle</th>
                    <th width="25%" class="text-right">Importe mensual</th>
                </tr>
            </thead>

            <tbody>
                @forelse($serviciosContratados as $servicio)
                    <tr>
                        <td><strong>{{ $servicio['tipo'] }}</strong></td>
                        <td>{{ $servicio['detalle'] }}</td>
                        <td class="service-price">${{ number_format($servicio['cantidad'] * $servicio['precio'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center muted">No hay servicios registrados.</td>
                    </tr>
                @endforelse

                @if($cantidadServicios > 0)
                    <tr class="service-total">
                        <td colspan="2" class="text-right">Total mensual de servicios</td>
                        <td class="service-price">${{ number_format($totalServicios, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section avoid-break">

        <div class="section-title">Historial de pagos del trimestre</div>
        @if($ultimosTres->count() > 0)
            <table class="history-table">
                <thead>
                    <tr>
                        <th width="20%">Fecha</th>
                        <th width="35%">Periodo</th>
                        <th width="25%">Venta</th>
                        <th width="20%" class="text-right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimosTres as $item)
                        <tr>
                            <td>{{ $item['fechaEmision'] ?? 'N/A' }}</td>
                            <td>{{ $item['mensualidad'] ?? 'N/A' }}</td>
                            <td>{{ $item['VENTA'] ?? 'N/A' }}</td>
                            <td class="amount">${{ number_format((float) ($item['importe'] ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="history-total">
                        <td colspan="3" class="text-right">Total trimestral</td>
                        <td class="amount">${{ number_format($totalTrimestral, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-debt">
                <div class="no-debt-title">Sin información</div>
                <div class="no-debt-text">No hay información disponible para los últimos tres meses. </div>
            </div>
        @endif
    </div>

    <div class="section avoid-break">
        <div class="section-title">Estado de la cuenta</div>
        @if($tieneDeuda)
            <div class="debt-warning">
                <table class="status-table">
                    <tr>
                        <td>
                            <div class="debt-title">SALDO PENDIENTE</div>
                            <div class="debt-text">
                                @if($mesesAdeudo > 0)
                                    Tienes aproximadamente<strong>{{ $mesesAdeudo }}
                                        {{ $mesesAdeudo === 1 ? 'mensualidad' : 'mensualidades' }}</strong>
                                    pendiente(s) de pago.
                                @else
                                    Tu cuenta presenta un saldo pendientede pago.
                                @endif
                            </div>
                        </td>
                        <td width="30%" class="text-right">
                            <div class="client-label">TOTAL A PAGAR</div>
                            <div class="status-value deuda">${{ number_format($deuda, 2) }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <div class="no-debt">
                <table class="status-table">
                    <tr>
                        <td>
                            <div class="no-debt-title">TU CUENTA SE ENCUENTRA AL CORRIENTE</div>
                            <div class="no-debt-text">No tienes saldo pendiente registrado.</div>
                        </td>
                        <td width="30%" class="text-right">
                            <div class="client-label">ESTADO</div>
                            <div class="status-value correcto">SIN ADEUDO</div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </div>

    <div class="section avoid-break">

        <div class="section-title">Formas de pago disponibles</div>
        <table class="payment-table">
            <tr>
                <td class="payment-cell">
                    <table class="payment-box">
                        <tr>
                            <td colspan="2" class="payment-header">Sucursal bancaria · HSBC
                        </tr>
                        <tr>
                            <td class="payment-image">
                                <img src="http://localhost:4200/assets/img/FormasPago/hsbc.webp" class="payment-logo">
                            </td>
                            <td class="payment-info">
                                <div class="payment-body">
                                    <div class="payment-label">Depósito en ventanilla o cajero</div>
                                    <div>
                                        <span class="payment-label">No. Cuenta:</span>
                                        <span class="payment-value">4062409131</span>
                                    </div>
                                    <div>
                                        <span class="payment-label">Beneficiario:</span>
                                        <span class="payment-value">IPTVTEL COMUNICACIONES</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                <td class="payment-cell">
                    <table class="payment-box">
                        <tr>
                            <td colspan="2" class="payment-header">Transferencia SPEI</td>
                        </tr>
                        <tr>
                            <td class="payment-image">
                                <img src="http://localhost:4200/assets/img/FormasPago/spei.webp" class="payment-logo">
                            </td>
                            <td class="payment-info">
                                <div class="payment-body">
                                    <div class="payment-label">Banca en línea o aplicación bancaria</div>
                                    <div>
                                        <span class="payment-label">CLABE:</span>
                                        <span class="payment-value">021453040624091311</span>
                                    </div>
                                    <div>
                                        <span class="payment-label">Beneficiario:</span>
                                        <span class="payment-value">IPTVTEL COMUNICACIONES</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>

        <table class="payment-box">
            <tr>
                <td colspan="2" class="oxxo-header">Tiendas de conveniencia</td>
            </tr>
            <tr>
                <td class="oxxo-image">
                    <img src="http://localhost:4200/assets/img/FormasPago/oxxo.webp" class="oxxo-logo">
                </td>
                <td class="oxxo-info">
                    <div class="oxxo-body">
                        Pago en efectivo en establecimientos participantes:<strong>OXXO · Farmacia del Ahorro · Financiera Bienestar</strong>
                        <br>
                        <span class="payment-label">No. Referencia:</span><strong>4741764001982278</strong>
                        <br>Tu pago se verá reflejado en menos de 24 hrs.
                        <br>Recuerda subir tu comprobante en la App o a nuestro Embot al <strong>713 347 5658</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong>IPTVTEL Comunicaciones S. DE R.L. DE C.V.</strong>· 713 133 4557 Ext 1 · clientes@emenet.mx
    </div>
</body>
</html>