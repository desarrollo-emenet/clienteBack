<?php

namespace App\Http\Controllers\Notificaciones;

use App\Http\Controllers\Controller;
use App\Models\UserDevicesToken;
use App\Service\Notificaciones\notifiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\FcmService;

class NotificacionPushController extends Controller
{
    //
    public function saveTokenNoti(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'nullable|string'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        Log::info('Token recibido desde Angular:', $request->all());


        $device = UserDevicesToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'user_id' => $user->id,
                'device_type' => $request->device_type ?? 'android'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token de notificación guardado exitosamente.',
            'data' => $device
        ], 200);
    }


    /*public function testPushNotification(Request $request, notifiService $fcmService)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'tipo'    => 'required|string' // 'pago' o 'deuda'
        ]);



        // mensajes adaptados según el tipo de notificación de angular
        $title = $request->tipo === 'pago' ? '¡Recordatorio de Pago!' : 'Deuda Pendiente Detectada';
        $body = $request->tipo === 'pago' ? 'Tu forma de pago requiere atención inmediata.' : 'Revisa tu estado de cuenta para ver los cargos.';

        //avisos en zona
        //


        $enviado = $fcmService->enviarNotificacionPush(
            $request->user_id,
            $title,
            $body,
            ['tipo' => $request->tipo]
        );

        if ($enviado) {
            return response()->json([
                'success' => true,
                'message' => 'Notificación de prueba enviada al usuario.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo enviar, verifica los logs de Laravel.'
        ], 400);
    }*/
}
