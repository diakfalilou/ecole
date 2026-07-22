<?php

namespace App\Pdf\AttestationNiveau;

class Body
{
    public static function render($pdf, $eleve, $ecole, $classe, $annee)
    {
        $pdf->Ln(3);

        $pdf->SetFont('helvetica', '', 11);

        $nomComplet = strtoupper($eleve->v_nom . ' ' . $eleve->v_prenom);

        // =========================
        // LIGNE 1
        // =========================
        $ligne1 = "Nous soussignés, Direction du groupe scolaire " .
            strtoupper($ecole->v_nomecole ?? '') . ",";

        $pdf->MultiCell(0, 10, $ligne1, 0, 'L');

        $pdf->Ln(0);

        // =========================
        // LIGNE 2
        // =========================
        $ligne2 = "certifions que l'élève " . $nomComplet . ",";

        $pdf->MultiCell(0, 10, $ligne2, 0, 'L');

        $pdf->Ln(0);

        // =========================
        // LIGNE 3
        // =========================
        $ligne3 = "inscrit(e) en classe de " . strtoupper($classe) .
            " pour l'année scolaire " . $annee . ",";

        $pdf->MultiCell(0, 10, $ligne3, 0, 'L');

        $pdf->Ln(0);

        // =========================
        // LIGNE 4
        // =========================
        $ligne4 = "a régulièrement suivi les cours au sein de notre établissement.";

        $pdf->MultiCell(0, 10, $ligne4, 0, 'L');

        $pdf->Ln(5);

        // =========================
        // CONCLUSION
        // =========================
        $pdf->MultiCell(
            0,
            10,
            "En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.",
            0,
            'L'
        );

        $pdf->Ln(12);
    }
}
