<?php

namespace App\Pdf\AttestationNiveau;

class Signature
{
    public static function render($pdf, $ecole)
    {
        $pdf->Ln(10);

        // =========================
        // DATE (DROITE)
        // =========================
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, "Fait à Conakry, le " . date('d/m/Y'), 0, 1, 'R');

        $pdf->Ln(6);

        // =========================
        // QR CODE (GAUCHE)
        // =========================
        $qrData = "ECOLE:" . $ecole->v_nomecole .
            "|DIRECTEUR:" . ($ecole->v_nomdirecteurecole ?? '') .
            "|DATE:" . date('d/m/Y');

        $pdf->write2DBarcode($qrData, 'QRCODE,H', 15, $pdf->GetY(), 30, 30);

        // =========================
        // SIGNATURE (DROITE)
        // =========================
        $pdf->SetXY(120, $pdf->GetY());

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, "Le Directeur", 0, 1, 'R');

        $pdf->Ln(12);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, strtoupper($ecole->v_nomdirecteurecole ?? ''), 0, 1, 'R');

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, "[ Cachet de l'établissement ]", 0, 1, 'R');
    }
}
