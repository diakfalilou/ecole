<?php

namespace App\Pdf\CertificatScolarite;

class Title
{
    public static function render($pdf, $numero)
    {
        // =========================
        // ESPACE APRÈS HEADER
        // =========================
        $pdf->Ln(3);

        // =========================
        // LIGNE POINTILLÉE HAUTE
        // =========================
        $pageWidth = $pdf->getPageWidth();
        $margins   = $pdf->getMargins();

        $x1 = $margins['left'];
        $x2 = $pageWidth - $margins['right'];
        $y  = $pdf->GetY();

        $pdf->setLineStyle([
            'width' => 0.3,
            'dash'  => '2,2',
            'color' => [80,80,80]
        ]);

        $pdf->Line($x1, $y, $x2, $y);

        $pdf->Ln(6);

        // =========================
        // TITRE PRINCIPAL
        // =========================
        $pdf->SetFont('courier', 'B', 18);

        $pdf->Cell(
            0,
            10,
            "CERTIFICAT DE SCOLARITE",
            0,
            1,
            'C'
        );

        // =========================
        // NUMÉRO
        // =========================
        $pdf->SetFont('courier', '', 11);

        $pdf->Cell(
            0,
            7,
            "N° " . $numero,
            0,
            1,
            'C'
        );

        // =========================
        // LIGNE BASSE
        // =========================
        $pdf->Ln(2);

        $y = $pdf->GetY();

        $pdf->Line($x1, $y, $x2, $y);

        // Retour au style normal
        $pdf->setLineStyle([
            'width' => 0.2
        ]);

        // $pdf->Ln(8);
    }
}
