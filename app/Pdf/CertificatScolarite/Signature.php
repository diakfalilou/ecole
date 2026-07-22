<?php

namespace App\Pdf\CertificatScolarite;

class Signature
{
    public static function render($pdf, $ecole, $qrData)
    {
        $pdf->Ln(5);

        // ============================================
        // POSITION DE DÉPART
        // ============================================
        $startY = $pdf->GetY();

        // ============================================
        // QR CODE (GAUCHE)
        // ============================================
        $pdf->write2DBarcode(
            $qrData,
            'QRCODE,H',
            18,
            $startY,
            28,
            28
        );

        // Texte sous le QR Code
        $pdf->SetFont('courier', '', 7);
        $pdf->SetXY(10, $startY + 30);
        $pdf->Cell(
            44,
            4,
            "Verification en ligne",
            0,
            1,
            'C'
        );

        // ============================================
        // PARTIE DROITE
        // ============================================
        $pdf->SetXY(110, $startY);

        $pdf->SetFont('times', '', 11);

        $pdf->Cell(
            80,
            6,
            "Fait à Conakry, le " . date('d/m/Y'),
            0,
            1,
            'C'
        );

        $pdf->Ln(2);

        $pdf->SetX(110);

        $pdf->SetFont('times', 'B', 11);

        $pdf->Cell(
            80,
            6,
            "Le Directeur",
            0,
            1,
            'C'
        );

        // ============================================
        // ESPACE SIGNATURE
        // ============================================
        $pdf->Ln(18);

        $pdf->SetX(110);

        $pdf->SetFont('times', 'BU', 11);

        $pdf->Cell(
            80,
            6,
            strtoupper($ecole->v_nomdirecteurecole),
            0,
            1,
            'C'
        );

        // ============================================
        // CACHET
        // ============================================
        $pdf->Ln(4);

        $pdf->SetX(110);

        $pdf->SetFont('helvetica', 'I', 9);

        $pdf->Cell(
            80,
            6,
            "[ Cachet de l'établissement ]",
            0,
            1,
            'C'
        );

        // ============================================
        // LIGNE DE SÉPARATION
        // ============================================
        $pdf->Ln(6);

        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();

        $pdf->setLineStyle([
            'width' => 0.2,
            'dash'  => '2,2'
        ]);

        $pdf->Line(
            $margins['left'],
            $pdf->GetY(),
            $pageWidth - $margins['right'],
            $pdf->GetY()
        );

        $pdf->setLineStyle(['width' => 0.2]);

        $pdf->Ln(3);

        // ============================================
        // MENTION DE SÉCURITÉ
        // ============================================
        $pdf->SetFont('helvetica', 'I', 8);

        $pdf->MultiCell(
            0,
            4,
            "Ce certificat est un document officiel. Toute falsification ou reproduction frauduleuse est passible de poursuites conformément à la réglementation en vigueur.",
            0,
            'C'
        );
    }
}
