<?php

namespace App\Pdf\AttestationNiveau;

class Header
{
    public static function render($pdf, $ecole)
    {
        $pdf->SetY(10);

        // =========================
        // LOGO
        // =========================
        if (!empty($ecole->logo)) {
            $pdf->Image(
                public_path($ecole->logo),
                15,
                10,
                20
            );
        }

        // =========================
        // NOM ECOLE
        // =========================
        $pdf->SetFont('helvetica', 'B', 14);

        $nomEcole = $ecole->v_nomecole ?? 'ECOLE';

        $pdf->SetXY(40, 10);
        $pdf->Cell(130, 6, strtoupper($nomEcole), 0, 1, 'C');

        // =========================
        // INFOS ECOLE
        // =========================
        $pdf->SetFont('helvetica', '', 9);

        $adresse = $ecole->t_adresseecole ?? '';

        $tel1 = $ecole->v_telephone1ecole ?? '';
        $tel2 = $ecole->v_telephone2ecole ?? '';

        // ⚠️ champ email semble mélangé, on sécurise
        $email = $ecole->v_adressemailv_telephone1ecole ?? '';

        $info = trim(
            $adresse . ' | ' .
            $tel1 . ' / ' . $tel2 . ' | ' .
            $email
        );

        $pdf->SetX(40);
        $pdf->MultiCell(130, 4, $info, 0, 'C');

        // =========================
        // LIGNE DE SEPARATION
        // =========================
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, 28, 195, 28);

        $pdf->Ln(6);
    }
}
