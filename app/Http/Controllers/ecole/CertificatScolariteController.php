<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

require_once public_path('tcpdf/tcpdf.php');

class CertificatScolariteController extends Controller
{
    public function generate_certificat_scolarite($slug, $eleveId)
    {
        /* =========================
         * 1️⃣ DONNÉES
         * ========================= */
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        if (!$ecole) abort(404, 'École introuvable');

        $eleve = DB::table('tbleleve')
            ->where('i_eleve_id', $eleveId)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        if (!$eleve) abort(404, 'Élève introuvable');

        $inscription = DB::table('tblinscription')
            ->where('i_eleve_id', $eleveId)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_active', 1)
            ->first();
        if (!$inscription) abort(404, 'Inscription introuvable');

        $classe = DB::table('tblclasse')
            ->where('i_classe_id', $inscription->i_classe_id)
            ->first();

        $noCertificat = ($ecole->v_code_ecole ?? 'CERT') . '-' . date('Y') . '-' . str_pad($eleve->i_eleve_id, 4, '0', STR_PAD_LEFT);

        /* =========================
         * 2️⃣ PDF SETUP — PAYSAGE
         * ========================= */
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        // Couleurs
        $navyR = 13;  $navyG = 27;  $navyB = 76;
        $goldR  = 196; $goldG  = 155; $goldB  = 65;
        $lightBg = [245, 247, 252];

        // A4 paysage : 297 × 210
        $pageW = 297;
        $pageH = 210;

        /* =========================
         * 3️⃣ FOND DE PAGE
         * ========================= */
        $pdf->SetFillColor($lightBg[0], $lightBg[1], $lightBg[2]);
        $pdf->Rect(0, 0, $pageW, $pageH, 'F');

        /* =========================
         * 4️⃣ BORDURES DÉCORATIVES
         * ========================= */
        $pdf->SetFillColor($navyR, $navyG, $navyB);
        $pdf->Rect(0, 0, 10, $pageH, 'F');           // bande gauche
        $pdf->Rect($pageW - 10, 0, 10, $pageH, 'F'); // bande droite
        $pdf->Rect(0, 0, $pageW, 7, 'F');             // bande haute
        $pdf->Rect(0, $pageH - 7, $pageW, 7, 'F');   // bande basse

        // Lisérés dorés
        $pdf->SetDrawColor($goldR, $goldG, $goldB);
        $pdf->SetLineWidth(1.0);
        $pdf->Rect(11, 8, $pageW - 22, $pageH - 16, 'D');
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(13, 10, $pageW - 26, $pageH - 20, 'D');

        // Triangles décoratifs bas gauche / bas droite
        $pdf->SetFillColor($navyR, $navyG, $navyB);
        $pdf->Polygon([0, $pageH, 28, $pageH, 0, $pageH - 28], 'F');
        $pdf->Polygon([$pageW, $pageH, $pageW - 28, $pageH, $pageW, $pageH - 28], 'F');

        /* =========================
         * 5️⃣ LAYOUT DEUX COLONNES
         *    Colonne gauche  : en-tête + intro + signatures
         *    Colonne droite  : tableau élève + QR
         * ========================= */
        $pad   = 15;   // marge intérieure après bordure
        $inner = $pageW - 2 * $pad;  // largeur utile = 267mm

        $leftColW  = 118;   // colonne gauche
        $colGap    = 5;
        $rightColW = $inner - $leftColW - $colGap;  // colonne droite ≈ 144mm
        $leftColX  = $pad;
        $rightColX = $pad + $leftColW + $colGap;
        $topY      = 12;

        /* =========================
         * 6️⃣ EN-TÊTE (colonne gauche)
         * ========================= */
        $headerH = 52;
        $pdf->SetFillColor(255, 255, 255);
        $pdf->RoundedRect($leftColX, $topY, $leftColW, $headerH, 2, '1111', 'F');

        // Logo
        if ($ecole->logo) {
            $pdf->Image(public_path($ecole->logo), $leftColX + 2, $topY + 2, 28, 28, '', '', '', false, 300);
        }

        // Photo élève
        $photoW = 22; $photoH = 27;
        $photoX = $leftColX + $leftColW - $photoW - 2;
        $photoY = $topY + 2;
        if ($eleve->v_photo) {
            $pdf->SetDrawColor($navyR, $navyG, $navyB);
            $pdf->SetLineWidth(0.6);
            $pdf->RoundedRect($photoX - 1, $photoY - 1, $photoW + 2, $photoH + 2, 2, '1111', 'D');
            $pdf->Image(public_path($eleve->v_photo), $photoX, $photoY, $photoW, $photoH, '', '', '', false, 300);
        }

        // Textes centre en-tête
        $hCenterX = $leftColX + 32;
        $hCenterW = $leftColW - 32 - $photoW - 5;

        $pdf->SetXY($hCenterX, $topY + 3);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor($navyR, $navyG, $navyB);
        $pdf->Cell($hCenterW, 4, 'RÉPUBLIQUE DE GUINÉE', 0, 1, 'C');

        // Devise tricolore
        $deviseHtml = '<span style="color:#DC3232;font-weight:bold;">Travail</span>'
                    . '<span style="color:#C49B41;font-weight:bold;"> - </span>'
                    . '<span style="color:#F5A623;font-weight:bold;">Justice</span>'
                    . '<span style="color:#C49B41;font-weight:bold;"> - </span>'
                    . '<span style="color:#27AE60;font-weight:bold;">Solidarité</span>';
        $pdf->SetXY($hCenterX, $topY + 8);
        $pdf->writeHTMLCell($hCenterW, 4, $hCenterX, $topY + 8, $deviseHtml, 0, 1, false, true, 'C');

        $pdf->SetXY($hCenterX, $topY + 13);
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor($navyR, $navyG, $navyB);
        $pdf->Cell($hCenterW, 3.5, strtoupper($ecole->ministere ?? 'MINISTÈRE DE L\'ÉDUCATION NATIONALE'), 0, 1, 'C');

        // Séparateur
        $sCX = $hCenterX + $hCenterW / 2;
        $sY  = $topY + 18;
        $pdf->SetDrawColor($goldR, $goldG, $goldB);
        $pdf->SetLineWidth(0.4);
        $pdf->Line($hCenterX + 5, $sY, $sCX - 4, $sY);
        $pdf->Line($sCX + 4, $sY, $hCenterX + $hCenterW - 5, $sY);
        $pdf->SetFillColor($goldR, $goldG, $goldB);
        $pdf->Circle($sCX, $sY, 1.0, 0, 360, 'F');

        // Nom de l'école
        $pdf->SetXY($hCenterX, $topY + 20);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor($navyR, $navyG, $navyB);
        $pdf->Cell($hCenterW, 6, strtoupper($ecole->v_nomecole), 0, 1, 'C');

        // Slogan
        $pdf->SetXY($hCenterX, $topY + 27);
        $pdf->SetFont('helvetica', 'I', 7.5);
        $pdf->SetTextColor($navyR, $navyG, $navyB);
        $pdf->Cell($hCenterW, 4, $ecole->slogan ?? '', 0, 1, 'C');

        /* =========================
         * 7️⃣ BANDE CONTACTS (colonne gauche, sous en-tête)
         * ========================= */
        $contactY = $topY + $headerH + 2;
        $contactH = 14;
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(220, 220, 235);
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($leftColX, $contactY, $leftColW, $contactH, 2, '1111', 'FD');

        $contacts = [
            ['Adresse',    $ecole->v_adresse  ?? 'Conakry'],
            ['Téléphone',  $ecole->v_telephone ?? ''],
            ['Email',      $ecole->v_email     ?? ''],
            ['Code École', $ecole->v_code_ecole ?? ''],
        ];
        $cColW = $leftColW / 4;
        foreach ($contacts as $i => [$label, $val]) {
            $cx = $leftColX + $i * $cColW;
            if ($i > 0) {
                $pdf->SetDrawColor(200, 205, 225);
                $pdf->SetLineWidth(0.2);
                $pdf->Line($cx, $contactY + 2, $cx, $contactY + $contactH - 2);
            }
            // Icône (cercle)
            $pdf->SetFillColor($navyR, $navyG, $navyB);
            $pdf->Circle($cx + 5, $contactY + $contactH / 2, 3, 0, 360, 'F');
            // Label
            $pdf->SetXY($cx + 10, $contactY + 1.5);
            $pdf->SetFont('helvetica', 'B', 6);
            $pdf->SetTextColor($navyR, $navyG, $navyB);
            $pdf->Cell($cColW - 11, 3.5, $label, 0, 1, 'L');
            // Valeur
            $pdf->SetXY($cx + 10, $contactY + 5.5);
            $pdf->SetFont('helvetica', '', 5.5);
            $pdf->SetTextColor(60, 60, 80);
            $pdf->MultiCell($cColW - 11, 3, $val, 0, 'L');
        }

        /* =========================
         * 8️⃣ TITRE CERTIFICAT (colonne gauche)
         * ========================= */
        $titleY = $contactY + $contactH + 4;
        $this->drawOrnament($pdf, $leftColX + $leftColW / 2, $titleY, $goldR, $goldG, $goldB, 30);

        $pdf->SetXY($leftColX, $titleY + 3);
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor($navyR, $navyG, $navyB);
        $pdf->Cell($leftColW, 9, 'CERTIFICAT DE SCOLARITÉ', 0, 1, 'C');

        $this->drawOrnament($pdf, $leftColX + $leftColW / 2, $titleY + 13, $goldR, $goldG, $goldB, 30);

        /* =========================
         * 9️⃣ TEXTE INTRO (colonne gauche)
         * ========================= */
        $introY = $titleY + 17;
        $introHtml = "Je soussigné(e), Monsieur / Madame <b>{$ecole->v_nomdirecteurecole}</b>,<br>"
                   . "Directeur(trice) du <b>{$ecole->v_nomecole}</b>,<br>"
                   . "certifie que l'élève dont les informations suivent est<br>"
                   . "régulièrement inscrit(e) et fréquente normalement notre établissement.";
        $pdf->SetXY($leftColX + 2, $introY);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(40, 40, 60);
        $pdf->writeHTMLCell($leftColW - 4, 22, $leftColX + 2, $introY, $introHtml, 0, 1, false, true, 'C');

        /* =========================
         * 🔟 SIGNATURES (bas colonne gauche)
         * ========================= */
        $sigY    = $introY + 24;
        $sigColW = $leftColW / 3;

        // "Fait à"
        $ville   = $ecole->v_ville ?? 'Conakry';
        $dateStr = date('d') . ' ' . $this->moisFr(date('n')) . ' ' . date('Y');
        $pdf->SetXY($leftColX, $sigY);
        $pdf->writeHTMLCell($leftColW, 5, $leftColX, $sigY,
            "Fait à <b>$ville</b>, le <b>$dateStr</b>", 0, 1, false, true, 'C');

        // Le Directeur
        $pdf->SetXY($leftColX + 2, $sigY + 6);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor($navyR, $navyG, $navyB);
        $pdf->Cell($sigColW, 4, 'Le Directeur', 0, 1, 'L');

        if (!empty($ecole->v_signature) && file_exists(public_path($ecole->v_signature))) {
            $pdf->Image(public_path($ecole->v_signature), $leftColX + 2, $sigY + 11, 32, 14, '', '', '', false, 150);
        } else {
            $pdf->SetDrawColor(60, 60, 80);
            $pdf->SetLineWidth(0.3);
            $pdf->Line($leftColX + 2, $sigY + 22, $leftColX + 32, $sigY + 22);
        }
        $pdf->SetXY($leftColX + 2, $sigY + 24);
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(50, 50, 70);
        $pdf->Cell($sigColW, 4, $ecole->v_nomdirecteurecole, 0, 1, 'L');

        // Tampon (centre)
        $stampCX = $leftColX + $leftColW / 2;
        $stampCY = $sigY + 18;
        if (!empty($ecole->v_tampon) && file_exists(public_path($ecole->v_tampon))) {
            $pdf->Image(public_path($ecole->v_tampon), $stampCX - 14, $sigY + 5, 28, 28, '', '', '', false, 150);
        } else {
            $pdf->SetDrawColor($navyR, $navyG, $navyB);
            $pdf->SetLineWidth(0.6);
            $pdf->Circle($stampCX, $stampCY, 14, 0, 360, 'D');
            $pdf->SetLineWidth(0.2);
            $pdf->Circle($stampCX, $stampCY, 12, 0, 360, 'D');
            $pdf->SetXY($stampCX - 14, $stampCY - 4);
            $pdf->SetFont('helvetica', 'B', 5.5);
            $pdf->SetTextColor($navyR, $navyG, $navyB);
            $pdf->Cell(28, 4, strtoupper($ecole->v_nomecole), 0, 1, 'C');
            $pdf->SetXY($stampCX - 14, $stampCY);
            $pdf->Cell(28, 4, 'LE DIRECTEUR', 0, 1, 'C');
        }

        // Médaille (droite)
        $medalX = $leftColX + $leftColW - 28;
        $medalY = $sigY + 5;
        if (!empty($ecole->v_medaille) && file_exists(public_path($ecole->v_medaille))) {
            $pdf->Image(public_path($ecole->v_medaille), $medalX, $medalY, 24, 24, '', '', '', false, 150);
        } else {
            $mx = $medalX + 12;
            $my = $medalY + 12;
            $pdf->SetFillColor($goldR, $goldG, $goldB);
            $pdf->Circle($mx, $my, 12, 0, 360, 'F');
            $pdf->SetFillColor($navyR, $navyG, $navyB);
            $pdf->Circle($mx, $my, 9, 0, 360, 'F');
            $pdf->SetFillColor($goldR, $goldG, $goldB);
            $pdf->Circle($mx, $my, 5, 0, 360, 'F');
        }

        /* =========================
         * 1️⃣1️⃣ COLONNE DROITE : TABLEAU + QR
         * ========================= */
        $tableStartY = $topY;
        $rowH   = 9;
        $labelW = 42;
        $valueW = $rightColW - $labelW;

        $rows = [
            ['Nom',                strtoupper($eleve->v_nom)],
            ['Prénom',             $eleve->v_prenom],
            ['Matricule',          $eleve->v_matricule],
            ['Date de naissance',  $eleve->d_date_naissance ? date('d / m / Y', strtotime($eleve->d_date_naissance)) : ''],
            ['Classe',             $classe->v_nom_classe ?? ''],
            ['Année scolaire',     $inscription->v_annee_scolaire ?? ''],
            ['Type d\'inscription', $inscription->v_type_inscription ?? 'Inscription'],
            ['Date d\'inscription', $inscription->d_date_inscription ? date('d / m / Y', strtotime($inscription->d_date_inscription)) : ''],
        ];

        // Titre colonne droite
        $pdf->SetFillColor($navyR, $navyG, $navyB);
        $pdf->RoundedRect($rightColX, $tableStartY, $rightColW, 8, 2, '1100', 'F');
        $pdf->SetXY($rightColX, $tableStartY + 1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($rightColW, 6, 'INFORMATIONS DE L\'ÉLÈVE', 0, 1, 'C');

        // Lignes du tableau
        foreach ($rows as $idx => $row) {
            $rowY = $tableStartY + 8 + $idx * $rowH;

            $pdf->SetFillColor($navyR, $navyG, $navyB);
            $pdf->Rect($rightColX, $rowY, $labelW, $rowH, 'F');

            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect($rightColX + $labelW, $rowY, $valueW, $rowH, 'F');

            $pdf->SetDrawColor(200, 205, 225);
            $pdf->SetLineWidth(0.2);
            $pdf->Rect($rightColX, $rowY, $rightColW, $rowH, 'D');

            $pdf->SetXY($rightColX + 2, $rowY + 2);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell($labelW - 3, $rowH - 4, $row[0], 0, 0, 'L');

            $pdf->SetXY($rightColX + $labelW + 2, $rowY + 2);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor($navyR, $navyG, $navyB);
            $pdf->Cell($valueW - 3, $rowH - 4, $row[1], 0, 0, 'L');
        }

        // Boîte QR sous le tableau
        $tableEndY = $tableStartY + 8 + count($rows) * $rowH;
        $qrBoxY    = $tableEndY + 3;
        $qrBoxH    = $pageH - $qrBoxY - $pad - 2;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(200, 205, 225);
        $pdf->SetLineWidth(0.3);
        $pdf->RoundedRect($rightColX, $qrBoxY, $rightColW, $qrBoxH, 2, '1111', 'FD');

        // En-tête boîte QR
        $pdf->SetFillColor($navyR, $navyG, $navyB);
        $pdf->RoundedRect($rightColX, $qrBoxY, $rightColW, 8, 2, '1100', 'F');
        $pdf->SetXY($rightColX, $qrBoxY + 1);
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($rightColW, 6, 'VÉRIFICATION DU CERTIFICAT', 0, 1, 'C');

        // QR Code (centré, taille adaptée)
        $verificationUrl = url('/verifier-certificat/' . $eleve->v_matricule);
        $qrSize = min($rightColW / 2 - 6, $qrBoxH - 22);
        $qrX    = $rightColX + ($rightColW / 2 - $qrSize) / 2;
        $qrY2   = $qrBoxY + 10;
        $style  = ['border' => 0, 'padding' => 1, 'fgcolor' => [$navyR, $navyG, $navyB], 'bgcolor' => [255, 255, 255]];
        $pdf->write2DBarcode($verificationUrl, 'QRCODE,H', $qrX, $qrY2, $qrSize, $qrSize, $style);

        // Texte à droite du QR
        $qrTextX = $rightColX + $rightColW / 2 + 2;
        $qrTextW = $rightColW / 2 - 4;
        $pdf->SetXY($qrTextX, $qrY2 + 2);
        $pdf->SetFont('helvetica', 'I', 6.5);
        $pdf->SetTextColor(80, 80, 100);
        $pdf->MultiCell($qrTextW, 3.5, "Scannez ce QR Code pour vérifier l'authenticité du certificat.", 0, 'C');

        // Badge N° certificat
        $badgeY2 = $qrBoxY + $qrBoxH - 9;
        $pdf->SetFillColor($goldR, $goldG, $goldB);
        $pdf->RoundedRect($rightColX + 4, $badgeY2, $rightColW - 8, 7, 2, '1111', 'F');
        $pdf->SetXY($rightColX + 4, $badgeY2 + 1);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($rightColW - 8, 5, 'N° Certificat : ' . $noCertificat, 0, 0, 'C');

        /* =========================
         * 1️⃣2️⃣ PIED DE PAGE
         * ========================= */
        $pdf->SetXY($pad, $pageH - 13);
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($inner, 6, '"Former aujourd\'hui, réussir demain."', 0, 1, 'C');

        /* =========================
         * OUTPUT
         * ========================= */
        return $pdf->Output(
            'certificat_' . $eleve->v_matricule . '.pdf',
            'I'
        );
    }

    /**
     * Ornement doré (deux lignes + losange central)
     */
    private function drawOrnament(\TCPDF $pdf, float $cx, float $y, int $r, int $g, int $b, float $len = 40): void
    {
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->SetFillColor($r, $g, $b);
        $pdf->SetLineWidth(0.4);
        $pdf->Line($cx - $len, $y, $cx - 5, $y);
        $pdf->Line($cx + 5, $y, $cx + $len, $y);
        $s = 2.0;
        $pdf->Polygon([$cx, $y - $s, $cx + $s, $y, $cx, $y + $s, $cx - $s, $y], 'F');
    }

    /**
     * Nom du mois en français
     */
    private function moisFr(int $n): string
    {
        $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        return $mois[$n] ?? '';
    }
}
