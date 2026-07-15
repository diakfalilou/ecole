<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
require_once public_path('tcpdf/tcpdf.php');

use TCPDF;

class EmploiDuTempsController extends Controller
{



    // Génère le PDF de l'emploi du temps complet d'un prof pour une année scolaire
    public function imprimerPdf(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'professeur_id'  => 'required|integer',
            'annee_scolaire' => 'required|string',
        ]);

        $professeur = DB::table('tblproffesseur')->where('id', $request->professeur_id)->first();
        abort_if(!$professeur, 404, 'Professeur introuvable.');

        $planning = DB::table('tblemploidutemps as e')
            ->join('tblclasse as c', 'c.i_classe_id', '=', 'e.classe_id')
            ->join('tblmatiere as m', 'm.id', '=', 'e.matiere_id')
            ->where('e.ecole_id', $ecoleId)
            ->where('e.annee_scolaire', $request->annee_scolaire)
            ->where('e.professeur_id', $request->professeur_id)
            ->select('e.jour', 'e.heure_debut', 'c.v_nom_classe', 'm.nom as matiere_nom')
            ->get();

        // Regroupement rapide par "jour_heureDebut"
        $map = [];
        foreach ($planning as $row) {
            $cle = $row->jour . '_' . substr($row->heure_debut, 0, 5);
            $map[$cle] = $row->v_nom_classe . "\n" . $row->matiere_nom;
        }

        $jours = [
            'lundi' => 'Lundi', 'mardi' => 'Mardi', 'mercredi' => 'Mercredi',
            'jeudi' => 'Jeudi', 'vendredi' => 'Vendredi', 'samedi' => 'Samedi',
        ];
        $heures = [];
        for ($h = 8; $h < 18; $h++) {
            $heures[] = sprintf('%02d:00', $h);
        }

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Application École');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetTitle('Emploi du temps - ' . $professeur->nom . ' ' . $professeur->prenom);
        $pdf->SetMargins(8, 10, 8);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Emploi du temps - ' . $professeur->nom . ' ' . $professeur->prenom, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Annee scolaire : ' . $request->annee_scolaire, 0, 1, 'C');
        $pdf->Ln(4);

        $largeurJour = 25;
        $largeurPage = 277; // largeur utile approximative en paysage A4 avec marges de 8mm
        $largeurCase = ($largeurPage - $largeurJour) / count($heures);

        // En-tête du tableau
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(241, 245, 249);
        $pdf->Cell($largeurJour, 8, 'Jour', 1, 0, 'C', true);
        foreach ($heures as $h) {
            $hFin = sprintf('%02d:00', (int) substr($h, 0, 2) + 1);
            $pdf->Cell($largeurCase, 8, $h . '-' . $hFin, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Lignes (une par jour)
        $pdf->SetFont('helvetica', '', 6.5);
        foreach ($jours as $cleJour => $labelJour) {
            $pdf->SetFillColor(248, 250, 252);
            $pdf->Cell($largeurJour, 12, $labelJour, 1, 0, 'C', true);
            foreach ($heures as $h) {
                $cle = $cleJour . '_' . $h;
                $texte = $map[$cle] ?? '';
                $pdf->MultiCell($largeurCase, 12, $texte, 1, 'C', false, 0);
            }
            $pdf->Ln();
        }

        return response($pdf->Output('emploi_du_temps.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="emploi_du_temps.pdf"');
    }

    public function index($slug)
    {
        abort_unless(PermissionHelper::hasRoute('emploidutemps'), 403);
        return view('ecoles.proffesseurs.emploidutemps.index', compact('slug'));
    }

    private function getEcoleId($slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_if(!$ecole, 404, 'École introuvable pour ce slug : ' . $slug);
        return $ecole->i_idecole;
    }

    // ---------- Listes pour les selects ----------

    // Années scolaires disponibles (issues des contrats de l'école)
    public function getAnneesScolaires($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $annees = DB::table('tblcontrat')
            ->where('i_ecole_id', $ecoleId)
            ->select('v_annee_scolaire')
            ->distinct()
            ->orderByDesc('v_annee_scolaire')
            ->pluck('v_annee_scolaire');

        return response()->json(['success' => true, 'data' => $annees]);
    }

    // Professeurs actifs
    public function getProfesseurs($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $professeurs = DB::table('tblproffesseur')
            ->where('ecole_id', $ecoleId)
            ->where('statut', 'active')
            ->orderBy('nom')
            ->select('id', 'nom', 'prenom', 'matricule', 'specialite')
            ->get();

        return response()->json(['success' => true, 'data' => $professeurs]);
    }

    // Niveaux actifs
    public function getNiveaux($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecoleId)
            ->where('b_desabled', 1) // ⚠️ hypothèse : 1 = actif. Inverser si besoin.
            ->orderBy('v_niveaux')
            ->select('i_niveauID', 'v_niveaux')
            ->get();

        return response()->json(['success' => true, 'data' => $niveaux]);
    }

    // Classes d'un niveau
    public function getClassesParNiveau($slug, $niveauId)
    {
        $ecoleId = $this->getEcoleId($slug);

        $classes = DB::table('tblclasse')
            ->where('i_ecole_id', $ecoleId)
            ->where('i_niveau_id', $niveauId)
            ->where('b_desabled', 1) // ⚠️ même hypothèse que ci-dessus
            ->orderBy('v_nom_classe')
            ->select('i_classe_id', 'v_nom_classe')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    }

    // Matières affectées à une classe
    public function getMatieresParClasse($slug, $classeId)
    {
        $ecoleId = $this->getEcoleId($slug);

        $matieres = DB::table('tblclassematiere as cm')
            ->join('tblmatiere as m', 'm.id', '=', 'cm.matiere_id')
            ->where('cm.ecole_id', $ecoleId)
            ->where('cm.classe_id', $classeId)
            ->where('m.statut', 'active')
            ->orderBy('m.nom')
            ->select('m.id', 'm.nom', 'm.code', 'cm.volume_horaire')
            ->get();

        return response()->json(['success' => true, 'data' => $matieres]);
    }

    // ---------- Grille de l'emploi du temps ----------

    // Renvoie : 1) tout le planning du prof (toutes classes confondues)
    //           2) les créneaux de la classe sélectionnée occupés par un AUTRE prof
    public function getGrille(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'professeur_id'   => 'required|integer',
            'annee_scolaire'  => 'required|string',
            'classe_id'       => 'required|integer',
        ]);

        $planningProf = DB::table('tblemploidutemps as e')
            ->join('tblclasse as c', 'c.i_classe_id', '=', 'e.classe_id')
            ->join('tblmatiere as m', 'm.id', '=', 'e.matiere_id')
            ->where('e.ecole_id', $ecoleId)
            ->where('e.annee_scolaire', $request->annee_scolaire)
            ->where('e.professeur_id', $request->professeur_id)
            ->select('e.jour', 'e.heure_debut', 'e.heure_fin', 'e.classe_id', 'e.matiere_id',
                     'c.v_nom_classe', 'm.nom as matiere_nom')
            ->get();

        $planningClasseAutrePof = DB::table('tblemploidutemps as e')
            ->join('tblproffesseur as p', 'p.id', '=', 'e.professeur_id')
            ->join('tblmatiere as m', 'm.id', '=', 'e.matiere_id')
            ->where('e.ecole_id', $ecoleId)
            ->where('e.annee_scolaire', $request->annee_scolaire)
            ->where('e.classe_id', $request->classe_id)
            ->where('e.professeur_id', '!=', $request->professeur_id)
            ->select('e.jour', 'e.heure_debut', 'e.heure_fin',
                     DB::raw("CONCAT(p.nom, ' ', p.prenom) as prof_nom"), 'm.nom as matiere_nom')
            ->get();

        return response()->json([
            'success'              => true,
            'planning_prof'        => $planningProf,
            'planning_classe_autre'=> $planningClasseAutrePof,
        ]);
    }

    // ---------- Cocher / décocher une case ----------

    public function toggleCase(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'professeur_id'   => 'required|integer',
            'classe_id'       => 'required|integer',
            'matiere_id'      => 'required|integer',
            'annee_scolaire'  => 'required|string',
            'jour'            => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut'     => 'required',
            'heure_fin'       => 'required',
        ]);

        // Ce prof a-t-il déjà quelque chose sur ce créneau (n'importe quelle classe) ?
        $existant = DB::table('tblemploidutemps')
            ->where('ecole_id', $ecoleId)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('professeur_id', $request->professeur_id)
            ->where('jour', $request->jour)
            ->where('heure_debut', $request->heure_debut)
            ->first();

        if ($existant) {
            // C'est exactement la même classe/matière -> on décoche (suppression)
            if ($existant->classe_id == $request->classe_id && $existant->matiere_id == $request->matiere_id) {
                DB::table('tblemploidutemps')->where('id', $existant->id)->delete();
                return response()->json(['success' => true, 'action' => 'removed']);
            }

            // Contrainte 1 : le prof est déjà occupé ailleurs (autre classe/matière) sur ce créneau
            return response()->json([
                'success' => false,
                'message' => "Ce professeur est déjà affecté à une autre classe/matière sur ce créneau."
            ], 422);
        }

        // Contrainte 2 : la classe est-elle déjà occupée par un AUTRE prof sur ce créneau ?
        $classeOccupee = DB::table('tblemploidutemps')
            ->where('ecole_id', $ecoleId)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('classe_id', $request->classe_id)
            ->where('jour', $request->jour)
            ->where('heure_debut', $request->heure_debut)
            ->where('professeur_id', '!=', $request->professeur_id)
            ->first();

        if ($classeOccupee) {
            return response()->json([
                'success' => false,
                'message' => "Cette classe est déjà occupée par un autre professeur sur ce créneau."
            ], 422);
        }

        // Aucun conflit -> on enregistre
        DB::table('tblemploidutemps')->insert([
            'ecole_id'        => $ecoleId,
            'annee_scolaire'  => $request->annee_scolaire,
            'professeur_id'   => $request->professeur_id,
            'classe_id'       => $request->classe_id,
            'matiere_id'      => $request->matiere_id,
            'jour'            => $request->jour,
            'heure_debut'     => $request->heure_debut,
            'heure_fin'       => $request->heure_fin,
            'created_by'      => Auth::id(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);

        return response()->json(['success' => true, 'action' => 'added']);
    }
}
