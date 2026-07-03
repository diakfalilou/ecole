<?php

namespace App\Pdf\CertificatScolarite;

class Watermark
{
    public static function render($pdf, $ecole)
    {
        // Sauvegarde de l'état graphique
        $pdf->StartTransform();

        // Opacité faible
        $pdf->SetAlpha(0.08);

        // =========================
        // FILIGRANE LOGO
        // =========================
        if (!empty($ecole->logo)) {

            $logo = public_path('uploads/ecoles/' . $ecole->logo);

            if (file_exists($logo)) {

                // Logo centré
                $pdf->Image(
                    $logo,
                    55,     // X
                    85,     // Y
                    100,    // Largeur
                    100,    // Hauteur
                    '',
                    '',
                    '',
                    false,
                    300
                );
            }

        } else {

            // =========================
            // FILIGRANE TEXTE
            // =========================
            $pdf->SetAlpha(0.05);

            $pdf->SetFont('times', 'B', 38);

            // Rotation de 45°
            $pdf->Rotate(45, 105, 148);

            $pdf->SetXY(20, 130);

            $pdf->Cell(
                170,
                10,
                "CERTIFICAT DE SCOLARITE",
                0,
                0,
                'C'
            );
        }

        // Restauration
        $pdf->StopTransform();

        // Retour à une opacité normale
        $pdf->SetAlpha(1);
    }
}
