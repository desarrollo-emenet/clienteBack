<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Verificacion de correo</title>
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

    <!-- Wrapper table (compatible con la mayoría de clientes) -->
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="640"
                    style="max-width:640px; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 6px 20px rgba(18,24,35,0.06);"
                    cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td >
                            <img src="{{ $message->embed(public_path('img/emenetLogo.png')) }}" alt="Logo Emenet" width="150"

                                style="max-width: 200px; height: auto; display: block; margin: 0 auto;" />

                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="inner" style="padding:32px;">

                            <h2 style="margin:0 0 12px 0; font-size:20px; color:#0b2236; font-weight:700;">Verificacion de Correo</h2>

                            <p style="margin:0 0 18px 0; color:#283142; font-size:14px; line-height:1.6;">
                                Confirma el correo registrado a tu cuenta de<strong> EMENET Comunicaciones</strong>, para continuar haz click en el siguiente enlace
                            </p>

                            <p style="margin:0 0 18px 0; color:#5b6b7a; font-size:13px;">
                                Por seguridad, este enlace expirará en <strong>{{ config('auth.passwords.users.expire',60) }} minutos</strong>.
                            </p>


                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:22px 0; width:100%;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}"
                                            style="background-color:#0b6efd; color:#ffffff; padding:12px 22px; border-radius:6px; font-weight:600; display:inline-block; text-decoration:none; min-width:180px; box-shadow:0 6px 16px rgba(11,110,253,0.14);">
                                            Verificar correo 
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #eef1f5; margin:20px 0;">

                            <p style="margin:0 0 8px 0; color:#6b7681; font-size:13px;">
                                Si el botón no funciona, copia y pega la siguiente dirección en tu navegador:
                            </p>
                            <p style="word-break:break-all; color:#0b6efd; font-size:13px; margin:6px 0 0 0;">
                                <a href="{{ $url }}">{{ $url }}</a>
                            </p>

                        </td>
                    </tr>


                    <tr>
                        <td style="background:#f7f9fb; padding:18px 32px; font-size:13px; color:#475569;">
                            <table width="100%" role="presentation">
                                <tr>
                                    <td style="vertical-align:top; width: 212px;">
                                        <strong>Atención a clientes</strong><br>
                                        <div style="margin-top:6px;">Tel: <a href="tel:7131334557"
                                                style="color:#0b6efd; text-decoration:none;">713 133 4557</a></div>
                                    </td>
                                    <td style="vertical-align:top; text-align: center;width: 212px;">
                                        <strong>Correo</strong><br>
                                        <div style="margin-top:6px;"><a href="tel:7131334557"
                                                style="color:#0b6efd; text-decoration:none;">clientes@emenet.mx</a>
                                        </div>
                                    </td>
                                    <td style="vertical-align:top; text-align:right;width: 212px;">
                                        <strong>Pagina web</strong><br>
                                        <div style="margin-top:6px;"><a href="https://emenet.mx"
                                                style="color:#0b6efd;">emenet.mx</a></div>
                                    </td>
                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td
                            style="padding:18px 32px 28px 32px; text-align:center;background: lightgray; font-size:12px;">
                            © {{ date('Y') }} EMENET Comunicaciones. Todos los derechos reservados.<br>
                            <div style="margin-top:8px; font-size:11px;">
                                No compartas informacion personal
                            </div>
                        </td>
                    </tr>

                </table>


            </td>
        </tr>
    </table>

</body>

</html>