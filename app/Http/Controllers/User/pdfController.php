<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Service\Pdf\pdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class pdfController extends Controller
{
    protected $informepdf;

    public function __construct(pdfService $informepdf)
    {
        $this->informepdf = $informepdf;
    }
    public function informePdf(String $numero)
    {
        try {
            return $this->informepdf->informeTrimestral($numero);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el informe en pdf',
                "error" => $e->getMessage(),

            ], 500);
        }
    }
}
