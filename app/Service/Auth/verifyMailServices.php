<?php

namespace App\Service\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Clase de servicio para la verificación de correo electrónico
class verifyMailServices
{
    public $urlFrontend;
    public $token;
    //verificar correo electrónico
    public function verify(Request $request, string $id, string $hash)
    {
        // Buscar el usuario por ID
        $user = User::findOrFail($id);

        $email = $request->input('email');

        if (!$email) {
            $email = $user->getEmailForVerification();
        }

        // Verificar el hash
        if (! hash_equals($hash, sha1($email))) {
            return response()->json([
                'message' => 'Enlace inválido'
            ], 403);
        }

        //actualizacion de coorre

        if ($email !== $user->email) {

            // Verificar que el correo todavía esté disponible
            $correoExiste = User::where('email', $email)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($correoExiste) {
                return response()->json([
                    'message' => 'El correo electrónico ya está registrado.'
                ], 422);
            }

            Log::info('Actualizando correo del usuario',[
                    'usuario' => $user->id,
                    'correo_anterior' => $user->email,
                    'correo_nuevo' => $email,
                ]
            );

            // actualizamos
            $user->email = $email;
            $user->markEmailAsVerified();
            $user->save();
            event(new Verified($user));
            return $this->redirectToFrontend($user);
        }


        //Sin actualizacion de correo

        Log::info('Verificando correo para el usuario: ' . $user->email);

        // Verificar si el correo ya ha sido verificado
        if ($user->hasVerifiedEmail()) {
            Log::info('El correo ya había sido verificado para el usuario: ' . $user->email);
            $this->urlFrontend = env('FRONTEND_URL_LOCAL');
            return redirect($this->urlFrontend . '/email-verificado?yaVerificado=true');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
        Log::info('es verificado: ' . ($user->hasVerifiedEmail() ? 'Sí' : 'No'));

        return $this->redirectToFrontend($user);
    }

    //redireccion al frontend con token
    private function redirectToFrontend(User $user)
    {
        $this->urlFrontend = env('FRONTEND_URL_LOCAL');

        $this->token = Str::random(64);
        Cache::put("email_verified_{$this->token}", $user->id, now()->addMinutes(5));
        Log::info('Token almacenado en la caché: ' . $this->token);
        return redirect($this->urlFrontend . '/email-verificado?token=' . urlencode($this->token));
    }


    //valida el token de verificación de correo electrónico
    public function validarToken(Request $request)
    {
        // Obtener el token de la solicitud
        $token = $request->input('token');
        Log::info('Token recibido: ' . $token);

        // Verificar si el token existe en la caché
        if (!$token) {
            return response()->json(['valid' => false], 400);
        }

        Log::info('Verificando token en la caché: ' . $token);
        // Buscar el token en la caché
        $userId = Cache::get("email_verified_{$token}");

        // Si el token es válido, devolver true y eliminarlo de la caché
        if ($userId) {
            Cache::forget("email_verified_{$token}");
            return response()->json(['valid' => true]);
        }

        // Si el token no es válido, devolver false
        return response()->json(['valid' => false], 403);
    }
}
