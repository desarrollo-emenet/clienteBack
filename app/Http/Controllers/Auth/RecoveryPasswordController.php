<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\Auth\recoveryService;


class RecoveryPasswordController extends Controller
{
    private $recoveryService;
    public function __construct(recoveryService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    public function sendEmail(Request $request)
    {
        try{
            $this->recoveryService->sendEmail($request);
        }catch(\Exception $e){
            return response()->json([
                "status" => "false",
                "message" => "Ocurrió un error al enviar el correo de recuperación. " . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try{
            return $this->recoveryService->updatePassword($request);
        }catch(\Exception $e){
            return response()->json([
                "status" => "false",
                "message" => "Ocurrió un error al actualizar la contraseña. " . $e->getMessage()
            ], 500);
        }
    }
}
