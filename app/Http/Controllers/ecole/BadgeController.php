<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

require_once public_path('tcpdf/tcpdf.php');

class BadgeController extends Controller
{
    public function generate_badge($slug, $eleveId)
    {
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        $eleve = DB::table('tbleleve')
            ->where('i_eleve_id', $eleveId)
            ->first();

        $qrData = url("/verify-eleve/{$eleve->i_eleve_id}");

        $pdf = new \TCPDF('P', 'mm', [54, 85.6], true, 'UTF-8', false);

        $pdf->SetMargins(2, 2, 2);
        $pdf->SetAutoPageBreak(false);

        // =========================
        // PAGE 1 - RECTO
        // =========================
        $pdf->AddPage();

        $pdf->SetLineWidth(0.3);
        $pdf->Rect(1, 1, 52, 83.6);

        // --- TRICOLORE ---
        $pdf->SetFillColor(220, 0, 0);
        $pdf->Polygon([0,0, 20,0, 54,85, 34,85], 'F');

        $pdf->SetFillColor(255, 200, 0);
        $pdf->Polygon([0,5, 16,5, 50,85, 34,85], 'F');

        $pdf->SetFillColor(0, 150, 0);
        $pdf->Polygon([0,10, 12,10, 46,85, 34,85], 'F');

        // --- MINISTERE ---
        $pdf->SetFont('helvetica', '', 5);
        $pdf->SetXY(2, 2);
        $pdf->MultiCell(50, 3, $ecole->ministere ?? '', 0, 'C');

        // --- LOGO ---
        if (!empty($ecole->logo)) {
            $pdf->Image(public_path('uploads/ecoles/' . $ecole->logo), 20, 8, 12);
        }

        // --- ECOLE ---
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(2, 20);
        $pdf->Cell(50, 4, strtoupper($ecole->v_nomecole), 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell(50, 3, $ecole->slogan ?? '', 0, 1, 'C');

        // --- PHOTO ---
        if (!empty($eleve->v_photo)) {
            $pdf->Image(public_path($eleve->v_photo), 18, 32, 18, 22);
        }

        // --- ELEVE ---
        $pdf->SetFont('courier', '', 7);
        $pdf->SetXY(2, 56);

        $pdf->Cell(50, 4, "Nom: " . strtoupper($eleve->v_nom), 0, 1, 'C');
        $pdf->Cell(50, 4, "Prenom: " . strtoupper($eleve->v_prenom), 0, 1, 'C');
        $pdf->Cell(50, 4, "Matricule: " . $eleve->v_matricule, 0, 1, 'C');

        // --- QR RECTO ---
        $pdf->write2DBarcode($qrData, 'QRCODE,H', 18, 70, 18, 18);

        // =========================
        // PAGE 2 - VERSO
        // =========================
        $pdf->AddPage();

        $pdf->SetFillColor(245, 245, 245);
        $pdf->Rect(0, 0, 54, 85.6, 'F');

        // --- TITRE ---
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(2, 5);
        $pdf->Cell(50, 5, strtoupper($ecole->v_nomecole), 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell(50, 4, $ecole->slogan ?? '', 0, 1, 'C');

        $pdf->Ln(2);
        $pdf->Line(5, $pdf->GetY(), 49, $pdf->GetY());

        $pdf->Ln(4);

        // --- REGLEMENT ---
        $pdf->SetFont('helvetica', '', 6);

        $reglement = "
            - Ce badge est strictement personnel
            - À présenter obligatoirement
            - Toute perte doit être signalée
            - Toute falsification est sanctionnée
        ";

        $pdf->MultiCell(50, 3, $reglement, 0, 'L');

        $pdf->Ln(3);

        // --- CONTACT ---
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->Cell(50, 4, "CONTACT", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 6);

        $contact =
            "Tel: " . ($ecole->v_telephone1ecole ?? '') . "\n" .
            "Email: " . ($ecole->v_adressemailv_telephone1ecole ?? '') . "\n" .
            "Adresse: " . ($ecole->t_adresseecole ?? '');

        $pdf->MultiCell(50, 3, $contact, 0, 'C');

        $pdf->Ln(4);

        // --- QR VERSO ---
        $pdf->write2DBarcode($qrData, 'QRCODE,H', 18, 62, 18, 18);

        // --- FOOTER ---
        $pdf->SetFont('helvetica', '', 5);
        $pdf->SetXY(2, 80);
        $pdf->Cell(50, 3, "BADGE OFFICIEL - SYSTEME SCOLAIRE", 0, 0, 'C');

        return $pdf->Output("badge_vertical.pdf", "I");
    }
}
