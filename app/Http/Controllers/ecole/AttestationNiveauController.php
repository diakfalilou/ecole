<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Pdf\AttestationNiveau\Header;
use App\Pdf\AttestationNiveau\StudentCard;
use App\Pdf\AttestationNiveau\Title;
use App\Pdf\AttestationNiveau\Niveau;
use App\Pdf\AttestationNiveau\Body;
use App\Pdf\AttestationNiveau\Signature;
require_once public_path('tcpdf/tcpdf.php');

class CustomTCPDF extends \TCPDF
{
    public function Header() {}

    public function Footer()
    {
        $this->SetY(-30);

        $pageWidth = $this->getPageWidth();
        $marginLeft = $this->getMargins()['left'];
        $marginRight = $this->getMargins()['right'];
        $usableWidth = $pageWidth - $marginLeft - $marginRight;

        $lineY = $this->GetY() - 5;
        $this->SetLineWidth(0.3);
        $this->Line($marginLeft, $lineY, $marginLeft + $usableWidth, $lineY);

        $this->SetFont('times', '', 8);
        $text = "Adresse : Carrefour Kagbelen à côté de Ecobank - Dubréka - RÉP. de Guinée\n".
                "Téléphone : +224 620 28 21 83 | 657 00 24 23 | 654 80 94 46\n".
                "Email : abapro67@gmail.com   Site web : www.bapguinee.com";

        $this->MultiCell(0, 4, $text, 0, 'C', 0, 1);
    }
}

class AttestationNiveauController extends Controller
{
    public function generate_attestation_niveau($slug, $eleveId)
    {
        // 🔹 ECOLE
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        // 🔹 ELEVE
        $eleve = DB::table('tbleleve')
            ->where('i_eleve_id', $eleveId)
            ->first();

        $inscription = DB::table('tblinscription')
            ->where('i_eleve_id', $eleveId)
            ->first();

        // 🔹 DONNEES
        $annee = session('anneescolaire');

        $classe = DB::table('tblclasse')->where('i_classe_id',$inscription->i_classe_id)->value('v_nom_classe') ?? "Non défini";
        $niveau = DB::table('tblniveau')->where('i_niveauID',$inscription->i_niveau_id)->value('v_niveaux') ?? "Non défini";

        $numero = "ATN-" . date('Y') . "-" . rand(100000, 999999);

        // 🔹 INIT PDF
        $pdf = new CustomTCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 35);
        $pdf->AddPage();

        // =========================
        // CONSTRUCTION DOCUMENT
        // =========================

        Header::render($pdf, $ecole);

        // 👉 IMPORTANT : affichage élève
        StudentCard::render($pdf, $eleve);

        // 👉 PROCHAINES ETAPES
        Title::render($pdf, $numero);
        Niveau::render($pdf,$niveau, $classe, $annee);
        Body::render($pdf, $eleve,$ecole, $classe, $annee);
        Signature::render($pdf, $ecole);

        return $pdf->Output("attestation_niveau.pdf", "I");
    }
}
