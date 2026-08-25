<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe trimestral</title>
    <style>
        {!! file_get_contents(resource_path('views/pdf/informe.css')) !!}
    </style>
</head>

<body>
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
                    <div class="client-name">{{ ($nombreCliente) }}</div>
                    <div class="client-block">
                        <span class="client-label">Número de cliente:</span>
                        <span class="client-value cliente"><strong>{{ $numeroCliente }}</strong></span>
                    </div>

                    <div class="client-block">
                        <span class="client-label">Dirección:</span>
                        <span class="client-value">{{ ucwords(strtolower($direccion)) }}
                            @if($colonia), {{ $colonia }}@endif
                            @if($municipio), {{ $municipio }}@endif
                            @if($estado), {{ $estado }}@endif
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
                        <th width="25%">Fecha</th>
                        <th width="50%">Periodo</th>
                        <th width="25%" class="text-right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimosTres as $item)
                        <tr>
                            <td>{{ $item['fechaEmision'] ?? 'N/A' }}</td>
                            <td>{{ $item['mensualidad'] ?? 'N/A' }}</td>
                            <td class="amount">
                                ${{ number_format((float) ($item['importe'] ?? 0), 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="history-total">
                        <td colspan="2" class="text-right">Total trimestral</td>
                        <td class="amount">
                            ${{ number_format($totalTrimestral, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @else

            <div class="no-debt">
                <div class="no-debt-title">Sin información</div>
                <div class="no-debt-text">No hay información disponible para los últimos tres meses.</div>
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
                            <td colspan="2" class="payment-header">Sucursal bancaria · HSBC </td>
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
                        Pago en efectivo en establecimientos participantes:<strong>OXXO · Farmacia del Ahorro ·
                            Financiera Bienestar</strong>
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