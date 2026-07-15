<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class FicheNoteController extends Controller
{
    private function moisParPeriode(string $type, int $numero): array
    {
        $map = [
            'trimestre' => [
                1 => ['Octobre', 'Novembre', 'Décembre'],
                2 => ['Janvier', 'Février', 'Mars'],
                3 => ['Avril', 'Mai', 'Juin'],
            ],
            'semestre' => [
                1 => ['Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février'],
                2 => ['Mars', 'Avril', 'Mai', 'Juin'],
            ],
        ];

        return $map[$type][$numero] ?? [];
    }

    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    /**
     * Page de filtre (année / niveau / classe / période)
     */
    public function index($slug)
    {
        abort_unless(PermissionHelper::hasRoute('fiche-notes'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblanneesclaire')
            ->orderBy('i_idanneesclaire', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.fiche-notes.index', compact('slug', 'annee_courante', 'niveaux', 'data_anneescolaire'));
    }

    /**
     * Données nécessaires pour générer la/les fiche(s) vierge(s) :
     * - Maternelle/Primaire : "combiner" = true -> une seule fiche avec toutes les matières
     * - Collège/Lycée : "combiner" = false -> une fiche distincte par matière (saut de page)
     */
    public function getFiche(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'annee_scolaire' => 'required|string',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'periode_type'   => 'required|in:trimestre,semestre',
            'periode_numero' => 'required|integer',
        ]);

        $niveau = DB::table('tblniveau')
            ->where('i_niveauID', $request->niveau_id)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        abort_unless($niveau, 404, 'Niveau introuvable.');

        $classe = DB::table('tblclasse')
            ->where('i_classe_id', $request->classe_id)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        abort_unless($classe, 404, 'Classe introuvable.');

        $nomNiveau = mb_strtolower($niveau->v_niveaux);
        $combiner = str_contains($nomNiveau, 'maternelle') || str_contains($nomNiveau, 'primaire');
        $maxNote = $combiner ? 10 : 20;

        $mois = $this->moisParPeriode($request->periode_type, $request->periode_numero);

        $matieres = DB::table('tblclassematiere as cm')
            ->join('tblmatiere as m', 'm.id', '=', 'cm.matiere_id')
            ->where('cm.classe_id', $request->classe_id)
            ->where('m.ecole_id', $ecole->i_idecole)
            ->where('m.statut', 'active')
            ->select('m.id', 'm.nom')
            ->orderBy('m.nom')
            ->get();

        $eleves = DB::table('tblinscription as i')
            ->join('tbleleve as e', 'e.i_eleve_id', '=', 'i.i_eleve_id')
            ->where('i.i_classe_id', $request->classe_id)
            ->where('i.i_ecole_id', $ecole->i_idecole)
            ->where('i.v_annee_scolaire', $request->annee_scolaire)
            ->where('i.b_active', 1)
            ->where('e.b_desabled', 1)
            ->select('e.i_eleve_id as id', 'e.v_nom as nom', 'e.v_prenom as prenom')
            ->orderBy('e.v_nom')
            ->get();

        return response()->json([
            'ecole'          => ['nom' => $ecole->v_slugecole, 'logo' => $ecole->logo ? asset($ecole->logo) : null],
            'niveau'         => $niveau->v_niveaux,
            'classe'         => $classe->v_nom_classe,
            'annee_scolaire' => $request->annee_scolaire,
            'periode_type'   => $request->periode_type,
            'periode_numero' => $request->periode_numero,
            'mois'           => $mois,
            'max_note'       => $maxNote,
            'combiner'       => $combiner,
            'matieres'       => $matieres,
            'eleves'         => $eleves,
        ]);
    }
}
