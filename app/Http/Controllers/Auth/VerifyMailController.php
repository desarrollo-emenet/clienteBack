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
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        $urlFrontend = env('FRONTEND_URL_LOCAL');

        // Validar el hash del enlace
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Enlace inválido'], 403);
        }

        // Segunda visita: correo ya verificado → redirigir directo al mensaje informativo
        if ($user->hasVerifiedEmail()) {
            return redirect($urlFrontend . '/email-verificado?yaVerificado=true');
        }

        // Primera visita: marcar como verificado y generar token de sesión
        $user->markEmailAsVerified();
        event(new Verified($user));

        $token = Str::random(64);
        Cache::put("email_verified_{$token}", $user->id, now()->addMinutes(5));

        return redirect($urlFrontend . '/email-verificado?token=' . urlencode($token));
    }

    public function validarToken(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['valid' => false], 400);
        }

        $userId = Cache::get("email_verified_{$token}");

        if ($userId) {
            Cache::forget("email_verified_{$token}");
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 403);
    }
}

