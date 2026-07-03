<?php

namespace App\Pdf\CertificatScolarite;

class Header
{
    public static function render($pdf, $ecole)
    {
        // =========================
        // MINISTÈRE
        // =========================
        if (!empty($ecole->ministere)) {

            $pdf->SetFont('helvetica', '', 8);

            // Remonté légèrement
            $pdf->SetXY(20, 6);

            $pdf->MultiCell(
                170,
                4,
                strtoupper($ecole->ministere),
                0,
                'C'
            );
        }

        // =========================
        // LOGO
        // =========================
        if (!empty($ecole->logo)) {

            $logo = public_path($ecole->logo);

            if (file_exists($logo)) {

                $pdf->Image(
                    $logo,
                    12,   // X
                    16,   // Y (remonté)
                    34,   // Largeur
                    34    // Hauteur
                );
            }
        }

        // =========================
        // NOM DE L'ÉCOLE
        // =========================
        $pdf->SetFont('times', 'B', 16);

        // Remonté et recentré par rapport au logo
        $pdf->SetXY(48, 22);

        $pdf->Cell(
            142,
            8,
            strtoupper($ecole->v_nomecole),
            0,
            1,
            'C'
        );

        // =========================
        // CODE ÉCOLE
        // =========================
        if (!empty($ecole->v_codeecole)) {

            $pdf->Ln(1);

            $pdf->SetFont('courier', '', 10);

            $pdf->Cell(
                0,
                5,
                "Code établissement : " . strtoupper($ecole->v_codeecole),
                0,
                1,
                'C'
            );
        }

        // =========================
        // ADRESSE
        // =========================
        $pdf->SetFont('helvetica', '', 9);

        $adresse = trim($ecole->t_adresseecole ?? '');

        $telephone = trim(
            ($ecole->v_telephone1ecole ?? '') .
            (!empty($ecole->v_telephone2ecole)
                ? " / " . $ecole->v_telephone2ecole
                : '')
        );

        $email = trim($ecole->v_adressemailv_telephone1ecole ?? '');

        $pdf->Cell(
            0,
            5,
            $adresse,
            0,
            1,
            'C'
        );

        $pdf->Cell(
            0,
            5,
            "Tél. : " . $telephone,
            0,
            1,
            'C'
        );

        $pdf->Cell(
            0,
            5,
            "Email : " . $email,
            0,
            1,
            'C'
        );

        // =========================
        // LIGNE DÉCORATIVE
        // =========================
        $pdf->Ln(2);

        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();

        $x1 = $margins['left'];
        $x2 = $pageWidth - $margins['right'];
        $y = $pdf->GetY();

        $pdf->setLineStyle([
            'width' => 0.4,
            'dash'  => '2,2',
            'color' => [0, 0, 0],
        ]);

        $pdf->Line($x1, $y, $x2, $y);

        // Retour au style normal
        $pdf->setLineStyle([
            'width' => 0.2
        ]);

        // =========================
        // ESPACE AVANT LE CONTENU
        // =========================
        $pdf->Ln(12);
    }
}
