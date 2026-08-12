<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\Auth\recoveryService;
use App\Http\Requests\auth\recoveryRequest;
use App\Http\Requests\auth\tokenRequest;
use App\Http\Requests\auth\updateRequest;

class RecoveryPasswordController extends Controller
{
    private $recoveryService;
    public function __construct(recoveryService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    public function sendEmail(recoveryRequest $request)
    {
        try{
           return $this->recoveryService->sendEmail($request);
        }catch(\Throwable $e){
            return response()->json([
                "status" => "false",
                "message" => "Ocurrió un error al enviar el correo de recuperación.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function updatePassword(updateRequest $request)
    {
        try{
            return $this->recoveryService->updatePassword($request);
        }catch(\Throwable $e){
            return response()->json([
                "status" => "false",
                "message" => "Ocurrió un error al actualizar la contraseña.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function validarToken(tokenRequest $request){

    try {
        return $this->recoveryService->validarToken($request);
    } catch (\Throwable $e) {
        return response()->json([
                "status" => "false",
                "message" => "Ocurrió un error al actualizar la contraseña. " . $e->getMessage()
            ], 500);
    }
    }
}
