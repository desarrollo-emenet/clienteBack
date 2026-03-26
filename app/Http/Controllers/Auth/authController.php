<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Service\Auth\authService;
use Illuminate\Http\Request;
use Throwable;

class authController extends Controller
{
    private $authService;
    public function __construct(authService $authService)
    {
        $this->authService = $authService;
    }


    public function login(Request $request)
    {
        $request->validate([
            'cliente' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], ['cliente' => 'Cliente', 'password' => "Contraseña",]);

        try {
            return $this->authService->login($request);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Ocurrió un error al obtener la información. ' . $th->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        try {
            // $request->user()->tokens()->delete(); borrar todos los token creados del usuario autenticado
            auth('sanctum')->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'error',
                "mensaje" => "Cierre de sesión exitoso"
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Ocurrió un error al obtener la información. ' . $th->getMessage(),
            ], 500);
        }
    }
}
