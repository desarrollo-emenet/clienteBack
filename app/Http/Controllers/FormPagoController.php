<?php

namespace App\Http\Controllers;

use App\Mail\FormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FormPagoController extends Controller
{
    //

    public function send(Request $request)
    {
        //campos que tendra el formulario, validacion de los mismos
        $validator = Validator::make($request->all(), [
            //'cliente'   => 'required|string|max:10',
            //'nombre' => 'required|string|max:100',
            'formaPago' => 'required|string',
            'fechaPago'   => 'required|date',
            'numOperacion'  => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'clave' => 'required|string|max:100',
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            //'mensualidad' => 'required|string|max:20',
            'monto' => 'required|string|max:20',
        ]);

        //si la validacion falla, se regresa un error con los mensajes de validacion
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // enviar info al endpoint
        try {

            Log::info('Enviando informacion con los siguientes datos: ', $request->all());

            //enviar informacion del formualario a la api con post
            Mail::to(config('mail.to_address'))->send(
                new FormMail($validator->validated())
            );
            

            return response()->json([
                'success' => true,
                'message' => 'informacion enviada correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la informacion.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
