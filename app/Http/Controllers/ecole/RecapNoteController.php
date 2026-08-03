<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class RecapNoteController extends Controller
{
    // Même répartition des mois que côté JS, pour rester cohérent
    private const MOIS_PAR_PERIODE = [
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

    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    public function recap_note($slug)
    {
        abort_unless(PermissionHelper::hasRoute('recap.note'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblcontrat')
            ->orderBy('i_contrat_id', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annee_scolaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        $ecoleInfo = [
            'nom'        => $ecole->v_nomecole,
            'logo'       => $ecole->logo,
            'adresse'    => $ecole->t_adresseecole,
            'telephone1' => $ecole->v_telephone1ecole,
            'telephone2' => $ecole->v_telephone2ecole,
            'directeur'  => $ecole->v_nomdirecteurecole,
            'slogan'     => $ecole->slogan,
            'ministere'  => $ecole->ministere,
        ];

        return view('ecoles.notes.recap_note', compact('data_anneescolaire', 'niveaux', 'annee_courante', 'slug', 'ecoleInfo'));
    }

    // AJAX : classes actives liées à un niveau
    public function classes_par_niveau($slug, $niveauId)
    {
        $ecole = $this->getEcole($slug);

        $classes = DB::table('tblclasse')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('i_niveau_id', $niveauId)
            ->where('b_desabled', 1) // 0 = classe active
            ->orderBy('v_nom_classe', 'asc')
            ->get(['i_classe_id', 'v_nom_classe']);

        return response()->json($classes);
    }

    // AJAX : matières rattachées à une classe (avec coefficient)
    public function matieres_par_classe($slug, $classeId)
    {
        $ecole = $this->getEcole($slug);

        $matieres = DB::table('tblclassematiere')
            ->join('tblmatiere', 'tblmatiere.id', '=', 'tblclassematiere.matiere_id')
            ->where('tblclassematiere.classe_id', $classeId)
            ->where('tblclassematiere.ecole_id', $ecole->i_idecole)
            ->where('tblmatiere.statut', 'active')
            ->select('tblmatiere.id', 'tblmatiere.nom', 'tblclassematiere.coefficient')
            ->orderBy('tblmatiere.nom')
            ->get();

        return response()->json($matieres);
    }

    // AJAX : élèves + matières + notes brutes de la période, pour calcul du récap côté client
    public function chargerRecap(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $data = $request->validate([
            'annee_scolaire' => 'required|string',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'periode_type'   => 'required|in:trimestre,semestre',
            'periode_numero' => 'required|integer',
        ]);

        $mois = self::MOIS_PAR_PERIODE[$data['periode_type']][$data['periode_numero']] ?? [];

        $matieres = DB::table('tblclassematiere')
            ->join('tblmatiere', 'tblmatiere.id', '=', 'tblclassematiere.matiere_id')
            ->where('tblclassematiere.classe_id', $data['classe_id'])
            ->where('tblclassematiere.ecole_id', $ecole->i_idecole)
            ->where('tblmatiere.statut', 'active')
            ->select('tblmatiere.id', 'tblmatiere.nom', 'tblclassematiere.coefficient')
            ->orderBy('tblmatiere.nom')
            ->get();

        $eleves = DB::table('tblinscription')
            ->join('tbleleve', 'tbleleve.i_eleve_id', '=', 'tblinscription.i_eleve_id')
            ->where('tblinscription.i_classe_id', $data['classe_id'])
            ->where('tblinscription.v_annee_scolaire', $data['annee_scolaire'])
            ->where('tblinscription.b_active', 1)
            ->select('tbleleve.i_eleve_id as id', 'tbleleve.v_nom as nom', 'tbleleve.v_prenom as prenom')
            ->orderBy('tbleleve.v_nom')
            ->get();

        $notes = DB::table('tblnote')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('annee_scolaire', $data['annee_scolaire'])
            ->where('classe_id', $data['classe_id'])
            ->where('periode_type', $data['periode_type'])
            ->where('periode_numero', $data['periode_numero'])
            ->select('eleve_id', 'matiere_id', 'type', 'mois', 'note')
            ->get();

        return response()->json([
            'matieres' => $matieres,
            'eleves'   => $eleves,
            'notes'    => $notes,
            'mois'     => $mois,
        ]);
    }
}
