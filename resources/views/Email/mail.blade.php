<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title>Nuevo pago recibido</title>
</head>

<body
  style="margin:0; padding:0; background-color:#f2f4f7; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
    style="background:#f2f4f7; padding:28px 12px;">
    <tr>
      <td align="center">

        <table width="640" cellpadding="0" cellspacing="0" role="presentation"
          style="max-width:640px; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 6px 20px rgba(18,24,35,0.06);">

          <!-- HEADER -->
          <tr>
            <td style="padding:28px 32px 10px 32px;" align="center">
              <img src="{{ $message->embed(public_path('img/emenetLogo.png')) }}" alt="EMENET Comunicaciones"
                width="150" style="display:block; border:0; max-width:200px; height:auto;">
            </td>
          </tr>

          <!-- TITULO -->
          <tr>
            <td style="padding:0 32px 10px 32px;">
              <h2 style="margin:0; font-size:20px; color:#0b2236; font-weight:700;">
                Nuevo pago recibido
              </h2>
            </td>
          </tr>

          <!-- DESCRIPCIÓN -->
          <tr>
            <td style="padding:0 32px 24px 32px; font-size:14px; color:#4b5563; line-height:1.6;">
              Se ha recibido un nuevo pago desde el sistema de <strong>EMENET Comunicaciones</strong>.
              <br><br>
              A continuación se muestran los detalles del pago:
            </td>
          </tr>

          <!-- DATOS -->
          <tr>
            <td style="padding:0 32px 28px 32px;">

              <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280;">Cliente</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['cliente'] }}</span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280;">Teléfono</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['telefono'] }}</span>
                  </td>
                </tr>
                
                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280;">Clave</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['clave'] }}</span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280;">Fecha de pago</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['fechaPago'] }}</span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280;">Número de operación</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['numOperacion'] }}</span>
                  </td>
                </tr>



                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280;">Monto</span><br>
                    <span style="font-size:14px; color:#111827;">${{ $data['monto'] }}</span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:16px 0 0 0;">
                    <span style="font-size:12px; color:#6b7280;">Comprobante</span><br>
                    <span style="font-size:14px; color:#111827;">
                      <img src="{{ $message->embed($data['comprobante']) }}" alt="imagen"
                        style="max-width: 100%; height: auto;">
                    </span>
                  </td>
                </tr>

              </table>

            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>

</html>