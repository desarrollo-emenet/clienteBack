<?php

namespace App\Service\Pdf;

use App\Service\User\metadataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class pdfService
{

    protected $metadataService;


    public function __construct(metadataService $metadataService)
    {
        $this->metadataService = $metadataService;
    }

    public function informeTrimestral(String $numero)
    {
        $user = Auth::user();
        $datosCliente = $this->metadataService->getMetadataForCliente($numero, $user);

        $cliente = $datosCliente['cliente'] ?? [];
        $servicios = $datosCliente['servicios'] ?? [];

        $pdf = Pdf::loadView('pdf.informe', [
            'cliente' => $cliente,
            'servicios' => $servicios,
        ]);

        //return view('pdf.informe', compact('cliente', 'servicios'));

        $pdfIn = $pdf->output();

        return response($pdfIn, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="inform.pdf"',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }
}
