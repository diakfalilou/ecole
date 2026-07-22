<?php

namespace App\Pdf\CertificatScolarite;

class Body
{
    public static function render(
        $pdf,
        $eleve,
        $ecole,
        $niveau,
        $classe,
        $annee
    ) {

        $pdf->Ln(5);

        // =========================
        // TEXTE OFFICIEL
        // =========================
        $pdf->SetFont('times', '', 12);

        $nom = strtoupper(
            trim(
                ($eleve->v_nom ?? '') . ' ' .
                ($eleve->v_prenom ?? '')
            )
        );

        $texte =

        "Nous soussignés, Direction du " .
        strtoupper($ecole->v_nomecole) .

        ", certifions que l'élève " .

        $nom .

        ", matricule " .

        ($eleve->v_matricule ?? '') .

        ", est régulièrement inscrit(e) dans notre établissement " .

        "au titre de l'année scolaire " .

        $annee .

        ", en classe de " .

        strtoupper($classe) .

        " (Niveau : " .

        strtoupper($niveau) .

        ").";

        $pdf->MultiCell(
            0,
            9,
            $texte,
            0,
            'J',
            false,
            1
        );

        $pdf->Ln(5);

        // =========================
        // DEUXIÈME PARAGRAPHE
        // =========================
        $texte2 =

        "Le présent certificat de scolarité est délivré à l'intéressé(e) " .

        "pour servir et valoir ce que de droit, à toutes fins utiles.";

        $pdf->MultiCell(
            0,
            9,
            $texte2,
            0,
            'J',
            false,
            1
        );

        $pdf->Ln(5);

        // =========================
        // OBSERVATION
        // =========================
        $pdf->SetFont('times', 'I', 11);

        $pdf->MultiCell(
            0,
            8,
            "Ce certificat n'est valable que pour l'année scolaire mentionnée ci-dessus.",
            0,
            'C'
        );

        // $pdf->Ln(6);
    }
}
