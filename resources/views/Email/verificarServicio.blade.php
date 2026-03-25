<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Verificación de servicio</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f2f4f7;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #121212;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        a {
            color: #0b6efd;
            text-decoration: none;
        }

        img {
            border: 0;
            display: block;
        }

        @media only screen and (max-width:600px) {
            .inner {
                padding: 20px !important;
            }
        }
    </style>
</head>

<body style="background-color:#f2f4f7; padding:28px 12px;">

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">

                
                <table width="640"
                    style="max-width:640px; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 6px 20px rgba(18,24,35,0.06);"
                    cellpadding="0" cellspacing="0" role="presentation">

                    
                    <tr>
                        <td>
                            <img src="{{ $message->embed(public_path('img/emenetLogo.png')) }}"
                                alt="Logo Emenet" width="150"
                                style="max-width: 200px; height: auto; display: block; margin: 20px auto;" />
                        </td>
                    </tr>

                    
                    <tr>
                        <td class="inner" style="padding:32px;">

                            <h2 style="margin:0 0 12px 0; font-size:20px; color:#0b2236; font-weight:700;">
                                Verificación de servicio
                            </h2>

                            <p style="margin:0 0 18px 0; color:#283142; font-size:14px; line-height:1.6;">
                                Estás a punto de registrar un servicio nuevo en <strong>emenet Comunicaciones</strong>.
                                Para continuar, utiliza el siguiente código de verificación:
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:25px 0; width:100%;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="font-size:32px; letter-spacing:8px; font-weight:700; color:#0b6efd; background:#f1f5ff; padding:14px 20px; border-radius:8px; display:inline-block;">
                                            {{ $codigo }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px 0; color:#5b6b7a; font-size:13px; align-content:center;">
                                Este código expira en <strong>10 minutos</strong>. 
                                Si no solicitaste este registro, puedes ignorar este mensaje.
                            </p>

                            <hr style="border:none; border-top:1px solid #eef1f5; margin:20px 0;">

                            <p style="margin:0; color:#6b7681; font-size:12px;">
                                Por seguridad, no compartas este código con nadie.
                            </p>

                        </td>
                    </tr>

                    
                    <tr>
                        <td style="background:#f7f9fb; padding:18px 32px; font-size:13px; color:#475569;">
                            <table width="100%" role="presentation">
                                <tr>
                                    <td style="vertical-align:top;">
                                        <strong>Atención a clientes</strong><br>
                                        <div style="margin-top:6px;">
                                            Tel: <a href="tel:7131334557"
                                                style="color:#0b6efd;">713 133 4557</a>
                                        </div>
                                    </td>
                                    <td style="vertical-align:top; text-align:center;">
                                        <strong>Correo</strong><br>
                                        <div style="margin-top:6px;">
                                            <a href="mailto:clientes@emenet.mx"
                                                style="color:#0b6efd;">clientes@emenet.mx</a>
                                        </div>
                                    </td>
                                    <td style="vertical-align:top; text-align:right;">
                                        <strong>Página web</strong><br>
                                        <div style="margin-top:6px;">
                                            <a href="https://emenet.mx"
                                                style="color:#0b6efd;">emenet.mx</a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    
                    <tr>
                        <td
                            style="padding:18px 32px 28px 32px; text-align:center; background:lightgray; font-size:12px;">
                            © {{ date('Y') }} EMENET Comunicaciones. Todos los derechos reservados.<br>
                            <div style="margin-top:8px; font-size:11px;">
                                No compartas información personal
                            </div>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>