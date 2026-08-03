<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class saisieNoteController extends Controller
{
    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    public function saisie_detail_note($slug)
    {
        abort_unless(PermissionHelper::hasRoute('saisie.note'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblcontrat')
            ->orderBy('i_contrat_id', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annee_scolaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.notes.saisie_note', compact('data_anneescolaire', 'niveaux', 'annee_courante', 'slug'));
    }

    // AJAX : classes actives liées à un niveau, pour l'école courante
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

    // AJAX : matières rattachées à une classe (via tblclassematiere)
    public function matieres_par_classe($slug, $classeId)
    {
        $ecole = $this->getEcole($slug);

        $matieres = DB::table('tblclassematiere')
            ->join('tblmatiere', 'tblmatiere.id', '=', 'tblclassematiere.matiere_id')
            ->where('tblclassematiere.classe_id', $classeId)
            ->where('tblclassematiere.ecole_id', $ecole->i_idecole)
            ->where('tblmatiere.statut', 'active')
            ->select('tblmatiere.id', 'tblmatiere.nom')
            ->orderBy('tblmatiere.nom')
            ->get();

        return response()->json($matieres);
    }

    // AJAX : charge la liste des élèves de la classe avec leur note existante (si déjà saisie)
    public function chargerEleves(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $data = $request->validate([
            'annee_scolaire' => 'required|string',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'matiere_id'     => 'required|integer',
            'periode_type'   => 'required|in:trimestre,semestre',
            'periode_numero' => 'required|integer',
            'type_note'      => 'required|in:cours,compo',
            'mois'           => 'nullable|string',
        ]);

        if ($data['type_note'] === 'cours' && empty($data['mois'])) {
            return response()->json(['message' => 'Le mois est requis pour une note de cours.'], 422);
        }

        // Élèves inscrits actifs de la classe pour l'année sélectionnée
        $eleves = DB::table('tblinscription')
            ->join('tbleleve', 'tbleleve.i_eleve_id', '=', 'tblinscription.i_eleve_id')
            ->where('tblinscription.i_classe_id', $data['classe_id'])
            ->where('tblinscription.v_annee_scolaire', $data['annee_scolaire'])
            ->where('tblinscription.b_active', 1)
            ->select('tbleleve.i_eleve_id', 'tbleleve.v_nom', 'tbleleve.v_prenom')
            ->orderBy('tbleleve.v_nom')
            ->get();

        // Notes déjà existantes pour ce filtre (pour pré-remplir le tableau si déjà saisi)
        $notesExistantes = DB::table('tblnote')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('annee_scolaire', $data['annee_scolaire'])
            ->where('matiere_id', $data['matiere_id'])
            ->where('periode_type', $data['periode_type'])
            ->where('periode_numero', $data['periode_numero'])
            ->where('type', $data['type_note'])
            ->when($data['type_note'] === 'cours', function ($q) use ($data) {
                $q->where('mois', $data['mois']);
            })
            ->pluck('note', 'eleve_id');

        $resultat = $eleves->map(function ($eleve) use ($notesExistantes) {
            return [
                'eleve_id' => $eleve->i_eleve_id,
                'nom'      => $eleve->v_nom,
                'prenom'   => $eleve->v_prenom,
                'note'     => $notesExistantes[$eleve->i_eleve_id] ?? null,
            ];
        });

        return response()->json(['eleves' => $resultat]);
    }

    // AJAX : enregistrement des notes saisies manuellement
    public function enregistrerNotes(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $data = $request->validate([
            'annee_scolaire'    => 'required|string',
            'niveau_id'         => 'required|integer',
            'classe_id'         => 'required|integer',
            'matiere_id'        => 'required|integer',
            'periode_type'      => 'required|in:trimestre,semestre',
            'periode_numero'    => 'required|integer',
            'type_note'         => 'required|in:cours,compo',
            'mois'              => 'nullable|string',
            'lignes'            => 'required|array|min:1',
            'lignes.*.eleve_id' => 'required|integer',
            'lignes.*.note'     => 'nullable|numeric|min:0|max:20',
        ]);

        $now = now();
        $count = 0;

        foreach ($data['lignes'] as $ligne) {
            if ($ligne['note'] === null || $ligne['note'] === '') {
                continue;
            }

            DB::table('tblnote')->updateOrInsert(
                [
                    'ecole_id'       => $ecole->i_idecole,
                    'annee_scolaire' => $data['annee_scolaire'],
                    'eleve_id'       => $ligne['eleve_id'],
                    'matiere_id'     => $data['matiere_id'],
                    'periode_type'   => $data['periode_type'],
                    'periode_numero' => $data['periode_numero'],
                    'type'           => $data['type_note'],
                    'mois'           => $data['type_note'] === 'cours' ? $data['mois'] : null,
                ],
                [
                    'niveau_id'  => $data['niveau_id'],
                    'classe_id'  => $data['classe_id'],
                    'note'       => $ligne['note'],
                    'created_by' => Auth::id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $count++;
        }

        return response()->json(['success' => true, 'nb_notes_enregistrees' => $count]);
    }
}
