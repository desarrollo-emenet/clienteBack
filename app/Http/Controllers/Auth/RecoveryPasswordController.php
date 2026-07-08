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
        return response()->json(
            $this->recoveryService->sendEmail($request)
        );
    }

    public function updatePassword(Request $request)
    {
        return response()->json(
            $this->recoveryService->updatePassword($request)
        );
    }
}
