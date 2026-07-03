<?php

namespace App\Pdf\AttestationNiveau;

class StudentCard
{
    public static function render($pdf, $eleve)
    {
        $pdf->Ln(5);

        // =========================
        // TITRE SECTION
        // =========================
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, "INFORMATIONS DE L'ELEVE", 0, 1, 'C');

        $pdf->Ln(2);

        // =========================
        // POSITION DE BASE (SANS CADRE)
        // =========================
        $x = 15;
        $y = $pdf->GetY();

        // =========================
        // PHOTO ELEVE
        // =========================
        if (!empty($eleve->v_photo)) {
            $pdf->Image(
                public_path($eleve->v_photo),
                160,
                $y + 2,
                30,
                35
            );
        } else {
            $pdf->Rect(160, $y + 2, 30, 35);
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetXY(160, $y + 18);
            $pdf->Cell(30, 5, "PHOTO", 0, 0, 'C');
        }

        // =========================
        // INFOS ELEVE
        // =========================
        $pdf->SetFont('helvetica', '', 10);

        $pdf->SetXY($x, $y + 4);
        $pdf->Cell(80, 5, "Matricule : " . ($eleve->v_matricule ?? ''), 0, 1);

        $pdf->SetX($x);
        $pdf->Cell(80, 5, "Nom : " . ($eleve->v_nom ?? ''), 0, 1);

        $pdf->SetX($x);
        $pdf->Cell(80, 5, "Prénom : " . ($eleve->v_prenom ?? ''), 0, 1);

        $pdf->SetX($x);
        $pdf->Cell(80, 5, "Genre : " . ($eleve->v_genre ?? ''), 0, 1);

        // Date formatée
        $dateNaiss = !empty($eleve->d_date_naissance)
            ? date('d/m/Y', strtotime($eleve->d_date_naissance))
            : '';

        $pdf->SetX($x);
        $pdf->Cell(80, 5, "Date de naissance : " . $dateNaiss, 0, 1);

        $pdf->SetX($x);
        $pdf->Cell(80, 5, "Téléphone : " . ($eleve->v_telephone ?? ''), 0, 1);

        $pdf->SetX($x);
        $pdf->Cell(80, 5, "Adresse : " . ($eleve->v_adresse ?? ''), 0, 1);

        $pdf->Ln(8);
    }
}
