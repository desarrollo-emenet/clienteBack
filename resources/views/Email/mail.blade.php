<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title>Nuevo mensaje de contacto</title>
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

          
          <tr>
            <td style="padding:0 32px 10px 32px;">
              <h2 style="margin:0; font-size:20px; color:#0b2236; font-weight:700;">
                Nuevo mensaje recibido desde el sitio web
              </h2>
            </td>
          </tr>

          
          <tr>
            <td style="padding:0 32px 24px 32px; font-size:14px; color:#4b5563; line-height:1.6;">
              Se ha generado una nueva solicitud de contacto a través del formulario web de
              <strong>EMENET Comunicaciones</strong>.
              <br><br>
              A continuación se detallan los datos proporcionados por el usuario:
            </td>
          </tr>

          
          <tr>
            <td style="padding:0 32px 28px 32px;">

              <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;">

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280; font-weight:600;">Nombre del contacto</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['nombre'] }}</span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280; font-weight:600;">Teléfono</span><br>
                    <span style="font-size:14px; color:#111827;">{{ $data['telefono'] }}</span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #eef1f5;">
                    <span style="font-size:12px; color:#6b7280; font-weight:600;">Correo electrónico</span><br>
                    <span style="font-size:14px; color:#111827;">
                      <a href="mailto:{{ $data['correo'] }}" style="color:#0b6efd; text-decoration:none;">
                        {{ $data['correo'] }}
                      </a>
                    </span>
                  </td>
                </tr>

                <tr>
                  <td style="padding:16px 0 0 0;">
                    <span style="font-size:12px; color:#6b7280; font-weight:600;">Mensaje enviado</span>
                    <div
                      style="margin-top:8px; background:#f9fafb; padding:14px; border-radius:6px; font-size:14px; color:#1f2937; line-height:1.6; white-space:pre-line;">
                      {{ $data['mensaje'] }}
                    </div>
                  </td>
                </tr>

              </table>

            </td>
          </tr>

          
          <tr>
            <td style="padding:0 32px 28px 32px; font-size:14px; color:#4b5563; line-height:1.6;">
              Se recomienda dar seguimiento oportuno a esta solicitud para mantener la calidad en la atención al
              cliente.
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>

</html>