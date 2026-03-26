<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VerifyMailController extends Controller
{
    public $urlFrontend;
    public $token;
    public function verify(Request $request, $id, $hash)
    {
        // Buscar el usuario por ID
        $user = User::findOrFail($id);
        $this->urlFrontend = env('FRONTEND_URL_LOCAL');

        //crear un token aleatorio para enviar al frontend
        $this->token = Str::random(64);

        // Verificar el hash
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Enlace inválido'], 403);
        }

        // Verificar si el correo ya ha sido verificado regresando al frontend
        if ($user->hasVerifiedEmail()) {
            Cache::put("email_verified_{token}", $user->id, now()->addMinutes(5));
            //Cache::put("email_verified_{$this->token}", $user->id, now()->addMinutes(5));
            //Log::info($token);
            //Log::info($this->urlFrontend . '/email-verificado?token={$token}' );

            return redirect($this->urlFrontend . '/email-verificado?token=' . urlencode($this->token));
        }

        // Marcar el correo como verificado
        $user->markEmailAsVerified();
        event(new Verified($user));

        //si no ha sido verificado antes, generar un token y enviarlo al frontend
        Cache::put("email_verified_{token}", $user->id, now()->addMinutes(5));
        //Cache::put("email_verified_{$this->token}", $user->id, now()->addMinutes(5));
        //Log::info($token);
        return redirect($this->urlFrontend . '/email-verificado?token=' . urlencode($this->token));

        
    }

    public function validarToken(Request $request)
    {
        // Obtener el token de la solicitud
        $token = $request->input('token');

        // Verificar si el token existe en la caché
        if (!$token) {
            return response()->json(['valid' => false], 400);
        }

        // Buscar el token en la caché
        $userId = Cache::get("email_verified_{token}");

        // Si el token es válido, devolver true y eliminarlo de la caché
        if ($userId) {
            Cache::forget("email_verified_{token}");
            return response()->json(['valid' => true]);
        }

        // Si el token no es válido, devolver false
        return response()->json(['valid' => false], 403);
    }
}
