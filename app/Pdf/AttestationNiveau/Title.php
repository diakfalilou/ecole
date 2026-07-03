<?php

namespace App\Pdf\AttestationNiveau;

class Title
{
    public static function render($pdf, $numero)
    {
        $pdf->Ln(2);

        // =========================
        // TITRE (STYLE DACTYLO)
        // =========================
        $pdf->SetFont('courier', 'B', 13);
        $pdf->Cell(0, 8, "ATTESTATION DE NIVEAU", 0, 1, 'C');

        // =========================
        // NUMERO (STYLE DACTYLO)
        // =========================
        $pdf->SetFont('courier', '', 11);
        $pdf->Cell(0, 6, "N° : " . $numero, 0, 1, 'C');

        // =========================
        // LIGNE POINTILLÉE PROPRE TCPDF
        // =========================
        $pdf->Ln(3);

        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();

        $x1 = $margins['left'];
        $x2 = $pageWidth - $margins['right'];
        $y  = $pdf->GetY();

        // dash style TCPDF correct
        $pdf->setLineStyle([
            'width' => 0.3,
            'cap'   => 'butt',
            'join'  => 'miter',
            'dash'  => '2,2',
            'color' => [0, 0, 0]
        ]);

        $pdf->Line($x1, $y, $x2, $y);

        // reset style (important)
        $pdf->setLineStyle(['width' => 0.3]);

        $pdf->Ln(0);
    }
}
