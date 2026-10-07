<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserDevicesToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeviceTokenController extends Controller
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
}
