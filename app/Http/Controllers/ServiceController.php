<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function verificarAcceso(Request $request, $numero)
    {
        $user = $request->user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return response()->json([
                'has_access' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        try {
            // Verificar que el numero_cliente pertenece al usuario
            $servicio = Service::where('numero_cliente', $numero)
                ->where('user_id', $user->id)
                ->first();

            // Si el servicio existe, el usuario tiene acceso
            if ($servicio) {
                return response()->json([
                    'has_access' => true,
                    'servicio' => [
                        'id' => $servicio->id,
                        'numero_cliente' => $servicio->numero_cliente,
                        'user_id' => $servicio->user_id
                    ]
                ], 200);
            } else {
                // Si no existe, el usuario no tiene acceso
                return response()->json([
                    'has_access' => false,
                    'message' => 'No tienes acceso a este servicio'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'has_access' => false,
                'message' => 'Error al verificar acceso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
