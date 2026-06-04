<?php

namespace App\Http\Controllers;

use App\Mail\FormMail;
use App\Service\Pagos\ApiPagosService;
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
            'cliente'   => 'required|string|max:10',
            'fechaPago'   => 'required|date',
            'numOperacion'  => 'required|string|max:100',
            'telefono' => 'required|string|max:10',
            'clave' => 'required|string|max:3', //TRH o DBH
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'monto' => 'required|numeric',
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

            //Log::info('Enviando informacion con los siguientes datos: ', $request->all());

            Log::info('Datos validados', $validator->validated());

            Log::info('Archivo recibido', [
                'nombre' => $request->file('comprobante')->getClientOriginalName(),
                'mime'   => $request->file('comprobante')->getMimeType(),
                'size'   => $request->file('comprobante')->getSize(),
            ]);

            //enviar datos a la api de pagos
            $response = ApiPagosService::peticionAPIPagos($request, $validator->validated());


            return response()->json([
                'success' => true,
                'response' => $response,
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
