<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\FormMail;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    //

    public function send(Request $request)
    {
        //campos que tendra el formulario, validacion de los mismos
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:100',
            'telefono' => 'required|string|max:15',
            'correo'   => 'required|email',
            'mensaje'  => 'required|string|max:2000',
        ]);

        //si la validacion falla, se regresa un error con los mensajes de validacion
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // enviar el correo, si hay un error se captura y se regresa un mensaje de error
        try {

            Log::info([
                'URL' => $request->fullUrl(),
                'METHOD' => $request->method(),
                'HEADERS' => $request->headers->all(),
                'BODY' => $request->all(),
            ]);

            //obtiene el correo del destinatario desde la configuracion, y envia el correo con los datos del formulario
            Mail::to(config('mail.to_address'))->send(
                new FormMail($request->only(['nombre', 'telefono', 'correo', 'mensaje']))
            );

            //Log::info('Correo enviado correctamente a ' . config('mail.to_address') . ' desde ' . $request->input('correo'));

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
