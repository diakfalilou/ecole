<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
require_once public_path('tcpdf/tcpdf.php');
class AttestationInscriptionReinscriptionController extends Controller
{
    //
    /**
     * Génère l'attestation d'inscription ou de réinscription
     * URL exemple : /attestation/{i_inscription_id}
     */
    public function genererAttestation($slug, $eleveId)
    {
        // =============================================
        // 1. RÉCUPÉRATION DES DONNÉES VIA QUERY BUILDER
        // =============================================

        $inscription = DB::table('tblinscription as ins')
            ->join('tbleleve as el', 'el.i_eleve_id', '=', 'ins.i_eleve_id')
            ->join('tblclasse as cl', 'cl.i_classe_id', '=', 'ins.i_classe_id')
            ->join('tbecole as ec', 'ec.i_idecole', '=', 'ins.i_ecole_id')
            ->select(
                // Inscription
                'ins.i_inscription_id',
                'ins.v_annee_scolaire',
                'ins.v_typeinscription',
                'ins.d_date_inscription',
                // Élève
                'el.i_eleve_id',
                'el.v_matricule',
                'el.v_nom',
                'el.v_prenom',
                'el.v_genre',
                'el.d_date_naissance',
                'el.v_adresse',
                // Classe
                'cl.v_nom_classe',
                // École
                'ec.v_nomecole',
                'ec.v_codeecole',
                'ec.t_adresseecole',
                'ec.v_telephone1ecole',
                'ec.v_nomdirecteurecole',
                'ec.logo',
                'ec.slogan',
                'ec.ministere'
            )
            ->where('ins.i_inscription_id', $eleveId)
            ->first();



        if (!$inscription) {
            abort(404, 'Inscription introuvable.');
        }

        // =============================================
        // 2. PRÉPARATION DES DONNÉES
        // =============================================

        $typeDoc   = strtolower($inscription->v_typeinscription) === 'reinscription'
            ? 'RÉINSCRIPTION'
            : 'INSCRIPTION';

        $nomComplet = strtoupper($inscription->v_nom) . ' ' . ucwords(strtolower($inscription->v_prenom));

        $dateNaissance = $inscription->d_date_naissance
            ? \Carbon\Carbon::parse($inscription->d_date_naissance)->translatedFormat('d F Y')
            : '-';

        $dateInscription = $inscription->d_date_inscription
            ? \Carbon\Carbon::parse($inscription->d_date_inscription)->translatedFormat('d F Y')
            : date('d/m/Y');

        $genre    = strtolower($inscription->v_genre) === 'f' ? 'Féminin' : 'Masculin';
        $articleE = strtolower($inscription->v_genre) === 'f' ? 'elle' : 'il';
        $articleL = strtolower($inscription->v_genre) === 'f' ? 'la' : 'le';

        $logoPath = $inscription->logo
            ? public_path($inscription->logo)
            : null;

        // =============================================
        // 3. GÉNÉRATION PDF AVEC TCPDF
        // =============================================

        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        // Métadonnées
        $pdf->SetCreator('Système de Gestion Scolaire');
        $pdf->SetAuthor($inscription->v_nomecole);
        $pdf->SetTitle('Attestation d\'' . ucfirst(strtolower($typeDoc)));
        $pdf->SetSubject('Attestation d\'inscription scolaire');

        // Marges & config
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        // ---- COULEURS ----
        $bleuFonce  = [0, 51, 102];
        $bleuClair  = [0, 102, 179];
        $grisLeger  = [245, 245, 245];
        $noir       = [0, 0, 0];
        $blanc      = [255, 255, 255];

        $pageW = $pdf->getPageWidth();   // 210
        $pageH = $pdf->getPageHeight();  // 297

        // =============================================
        // BORDURE DÉCORATIVE EXTÉRIEURE
        // =============================================
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetLineWidth(1.5);
        $pdf->Rect(10, 10, $pageW - 20, $pageH - 20, 'D');

        $pdf->SetDrawColor($bleuClair[0], $bleuClair[1], $bleuClair[2]);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(12, 12, $pageW - 24, $pageH - 24, 'D');

        // =============================================
        // EN-TÊTE : LOGO + INFOS ÉCOLE + MINISTÈRE
        // =============================================
        $pdf->SetY(18);

        // Logo (gauche)
        if ($logoPath && file_exists($logoPath)) {
            $pdf->Image($logoPath, 18, 18, 28, 28, '', '', '', false, 300);
        } else {
            // Placeholder logo
            $pdf->SetFillColor($bleuClair[0], $bleuClair[1], $bleuClair[2]);
            $pdf->Rect(18, 18, 28, 28, 'F');
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(18, 28);
            $pdf->Cell(28, 8, 'LOGO', 0, 0, 'C');
        }

        // Infos ministère & école (centre)
        $pdf->SetTextColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(50, 17);
        $pdf->MultiCell($pageW - 100, 4, strtoupper($inscription->ministere), 0, 'C', false, 1);

        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->SetTextColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetX(50);
        $pdf->Cell($pageW - 100, 7, strtoupper($inscription->v_nomecole), 0, 1, 'C');

        if ($inscription->slogan) {
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetX(50);
            $pdf->Cell($pageW - 100, 5, '"' . $inscription->slogan . '"', 0, 1, 'C');
        }

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetX(50);
        $pdf->Cell($pageW - 100, 4, 'Tél : ' . ($inscription->v_telephone1ecole ?? '-'), 0, 1, 'C');

        if ($inscription->t_adresseecole) {
            $pdf->SetX(50);
            $pdf->Cell($pageW - 100, 4, 'Adresse : ' . $inscription->t_adresseecole, 0, 1, 'C');
        }

        // Séparateur
        $pdf->SetY(50);
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(18, $pdf->GetY(), $pageW - 18, $pdf->GetY());
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(18, $pdf->GetY(), $pageW - 18, $pdf->GetY());
        $pdf->Ln(8);

        // =============================================
        // TITRE DU DOCUMENT
        // =============================================
        $pdf->SetFont('helvetica', 'BU', 16);
        $pdf->SetTextColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->Cell(0, 10, 'ATTESTATION D\'' . $typeDoc, 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'Année scolaire : ' . $inscription->v_annee_scolaire, 0, 1, 'C');
        $pdf->Ln(8);

        // =============================================
        // CORPS : TEXTE PRINCIPAL
        // =============================================

        // Bloc gris léger derrière le texte principal
        $yStart = $pdf->GetY();
        $pdf->SetFillColor($grisLeger[0], $grisLeger[1], $grisLeger[2]);
        $pdf->Rect(18, $yStart, $pageW - 36, 55, 'F');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetTextColor($noir[0], $noir[1], $noir[2]);
        $pdf->SetX(20);
        $pdf->SetY($yStart + 5);

        $texte = "Je soussigné(e), " . strtoupper($inscription->v_nomdirecteurecole ?? 'LE DIRECTEUR') .
            ", Directeur(trice) de " . $inscription->v_nomecole .
            ", atteste par la présente que l'élève :";

        $pdf->MultiCell($pageW - 40, 6, $texte, 0, 'J', false, 1);
        $pdf->Ln(4);

        // =============================================
        // BLOC INFO ÉLÈVE (encadré)
        // =============================================
        $yEleve = $pdf->GetY();
        $pdf->SetFillColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->Rect(25, $yEleve, $pageW - 50, 8, 'F');
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->SetTextColor($blanc[0], $blanc[1], $blanc[2]);
        $pdf->SetXY(25, $yEleve);
        $pdf->Cell($pageW - 50, 8, $nomComplet, 0, 1, 'C');
        $pdf->Ln(4);

        // Infos détaillées de l'élève
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor($noir[0], $noir[1], $noir[2]);

        $infoEleve = [
            ['Matricule',         $inscription->v_matricule ?? 'N/A'],
            ['Genre',             $genre],
            ['Date de naissance', $dateNaissance],
            ['Adresse',           $inscription->v_adresse ?? 'N/A'],
        ];

        foreach ($infoEleve as $item) {
            $pdf->SetX(28);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(50, 6, $item[0] . ' :', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 6, $item[1], 0, 1, 'L');
        }

        $pdf->Ln(4);

        // Suite du texte
        $pdf->SetX(20);
        $pdf->SetFont('helvetica', '', 11);

        $motTypeInsc = strtolower($inscription->v_typeinscription) === 'reinscription'
            ? 'régulièrement réinscrit(e)'
            : 'régulièrement inscrit(e)';

        $suite = "est " . $motTypeInsc . " dans notre établissement en classe de " .
            strtoupper($inscription->v_nom_classe) .
            " pour l'année scolaire " . $inscription->v_annee_scolaire .
            ", et que " . $articleE . " y suit normalement les cours.";

        $pdf->MultiCell($pageW - 40, 6, $suite, 0, 'J', false, 1);
        $pdf->Ln(4);

        $pdf->SetX(20);
        $pdf->MultiCell($pageW - 40, 6,
            "Cette attestation lui est délivrée pour servir et valoir ce que de droit.",
            0, 'J', false, 1
        );

        $pdf->Ln(10);

        // =============================================
        // CLASSE + DATE D'INSCRIPTION (tableau)
        // =============================================
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($bleuClair[0], $bleuClair[1], $bleuClair[2]);
        $pdf->SetTextColor($blanc[0], $blanc[1], $blanc[2]);
        $colW = ($pageW - 40) / 2;

        $pdf->SetX(20);
        $pdf->Cell($colW, 7, 'CLASSE', 1, 0, 'C', true);
        $pdf->Cell($colW, 7, 'DATE D\'INSCRIPTION', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor($grisLeger[0], $grisLeger[1], $grisLeger[2]);
        $pdf->SetTextColor($noir[0], $noir[1], $noir[2]);
        $pdf->SetX(20);
        $pdf->Cell($colW, 7, $inscription->v_nom_classe, 1, 0, 'C', true);
        $pdf->Cell($colW, 7, $dateInscription, 1, 1, 'C', true);

        $pdf->Ln(15);

        // =============================================
        // LIEU + DATE + SIGNATURE
        // =============================================
        $dateAujourdhui = \Carbon\Carbon::now()->translatedFormat('d F Y');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor($noir[0], $noir[1], $noir[2]);
        $pdf->SetX(20);
        $pdf->Cell(0, 6, 'Fait à _____________________, le ' . $dateAujourdhui, 0, 1, 'R');

        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Le Directeur(trice)', 0, 1, 'R');

        $pdf->Ln(20);

        // Ligne signature
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetLineWidth(0.5);
        $xSig = $pageW - 80;
        $pdf->Line($xSig, $pdf->GetY(), $pageW - 20, $pdf->GetY());
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetX($xSig);
        $pdf->Cell(60, 5, '(Signature & Cachet)', 0, 1, 'C');

        // =============================================
        // PIED DE PAGE PERSONNALISÉ
        // =============================================
        $pdf->SetY($pageH - 25);
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->Line(18, $pdf->GetY(), $pageW - 18, $pdf->GetY());
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 4, $inscription->v_nomecole . ' | ' . ($inscription->t_adresseecole ?? '') . ' | Tél : ' . ($inscription->v_telephone1ecole ?? ''), 0, 1, 'C');
        $pdf->Cell(0, 4, 'Document généré le ' . $dateAujourdhui . ' | Réf. N° ' . str_pad($eleveId, 6, '0', STR_PAD_LEFT), 0, 1, 'C');

        // =============================================
        // SORTIE DU PDF
        // =============================================
        $nomFichier = 'attestation_' . strtolower($typeDoc) . '_' . $inscription->v_matricule . '_' . date('Ymd') . '.pdf';

        $pdf->Output($nomFichier, 'I'); // 'I' = affiche dans le navigateur | 'D' = téléchargement
        exit;
    }
}
