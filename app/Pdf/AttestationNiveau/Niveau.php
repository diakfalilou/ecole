<?php

namespace App\Pdf\AttestationNiveau;

class Niveau
{
    public static function render($pdf, $niveau, $classe, $annee)
    {
        $pdf->Ln(3);

        // =========================
        // STYLE DACTYLO
        // =========================


        // largeur totale divisée en 3 blocs
        $pdf->SetFont('courier', 'B', 11);

        // 3 blocs équilibrés avec marge droite
        $pdf->Cell(60, 8, "NIVEAU : " . strtoupper($niveau), 0, 0, 'L');
        $pdf->Cell(60, 8, "CLASSE : " . strtoupper($classe), 0, 0, 'C');
        $pdf->Cell(60, 8, "ANNEE : " . $annee, 0, 1, 'L');

        // =========================
        // LIGNE POINTILLÉE PROPRE
        // =========================
        $pdf->Ln(2);

        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();

        $x1 = $margins['left'];
        $x2 = $pageWidth - $margins['right'];
        $y  = $pdf->GetY();

        $pdf->setLineStyle([
            'width' => 0.3,
            'dash'  => '2,2',
            'color' => [0, 0, 0]
        ]);

        $pdf->Line($x1, $y, $x2, $y);

        $pdf->setLineStyle(['width' => 0.3]);

        $pdf->Ln(6);
    }
}
