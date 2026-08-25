<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Service\Pdf\pdfService;
use Illuminate\Support\Facades\Log;

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
            Log::info($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el informe en pdf',
                "error" => $e->getMessage(),

            ], 500);
        }
    }
}
