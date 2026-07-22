<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Récupère l'école courante à partir du slug (utilisé dans toutes les méthodes)
     */
    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    public function index($slug)
    {
        abort_unless(PermissionHelper::hasRoute('notes'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblanneesclaire')
            ->orderBy('i_idanneesclaire', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.notes.index', compact('slug', 'annee_courante', 'niveaux', 'data_anneescolaire'));
    }

    /**
     * Classes liées à un niveau (pour le select dynamique)
     * ⚠️ Adapte le nom de table/colonnes selon ta table tblclasse réelle
     */
    public function getClassesByNiveau($slug, $niveauId)
    {
        $ecole = $this->getEcole($slug);

        $classes = DB::table('tblclasse')
            ->where('i_niveau_id', $niveauId)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_desabled', 1) // adapte si 1 = actif/activé chez toi, sinon retire cette ligne
            ->select('i_classe_id', 'v_nom_classe')
            ->orderBy('v_nom_classe')
            ->get();

        return response()->json($classes);
    }

    /**
     * Matières liées à une classe (via tblclassematiere)
     */
    public function getMatieresByClasse($slug, $classeId)
    {
        $ecole = $this->getEcole($slug);

        $matieres = DB::table('tblclassematiere as cm')
            ->join('tblmatiere as m', 'm.id', '=', 'cm.matiere_id')
            ->where('cm.classe_id', $classeId)
            ->where('m.ecole_id', $ecole->i_idecole)
            ->where('m.statut', 'active')
            ->select('m.id', 'm.nom', 'cm.coefficient')
            ->orderBy('m.nom')
            ->get();

        return response()->json($matieres);
    }

    /**
     * Élèves inscrits dans une classe pour une année scolaire donnée
     * (passage obligé par tblinscription, qui porte la relation élève <-> classe <-> année)
     */
    public function getElevesByClasse(Request $request, $slug, $classeId)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'annee_scolaire' => 'required|string',
        ]);

        $eleves = DB::table('tblinscription as i')
            ->join('tbleleve as e', 'e.i_eleve_id', '=', 'i.i_eleve_id')
            ->where('i.i_classe_id', $classeId)
            ->where('i.i_ecole_id', $ecole->i_idecole)
            ->where('i.v_annee_scolaire', $request->annee_scolaire)
            ->where('i.b_active', 1)
            ->where('e.b_desabled', 1) // adapte si 1 = actif/activé chez toi, sinon retire cette ligne
            ->select('e.i_eleve_id as id', 'e.v_nom as nom', 'e.v_prenom as prenom')
            ->orderBy('e.v_nom')
            ->get();

        return response()->json($eleves);
    }

    /**
     * Notes déjà saisies pour pré-remplir le tableau (cours + compo)
     */
    public function getNotes(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'annee_scolaire' => 'required',
            'niveau_id'      => 'required',
            'classe_id'      => 'required',
            'periode_type'   => 'required|in:trimestre,semestre',
            'periode_numero' => 'required|integer',
        ]);

        $notes = DB::table('tblnote')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('niveau_id', $request->niveau_id)
            ->where('classe_id', $request->classe_id)
            ->where('periode_type', $request->periode_type)
            ->where('periode_numero', $request->periode_numero)
            ->get();

        return response()->json($notes);
    }

    /**
     * Toutes les notes de l'année pour une classe, toutes périodes confondues
     * (utilisé pour construire le bulletin annuel : trimestre/semestre par trimestre/semestre + moyenne annuelle)
     */
    public function getRecapAnnuel(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'annee_scolaire' => 'required|string',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'periode_type'   => 'required|in:trimestre,semestre',
        ]);

        $notes = DB::table('tblnote')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('niveau_id', $request->niveau_id)
            ->where('classe_id', $request->classe_id)
            ->where('periode_type', $request->periode_type)
            ->get();

        return response()->json($notes);
    }

    /**
     * Enregistrement / mise à jour en masse (cours + compo)
     * Utilise updateOrInsert : crée la ligne si elle n'existe pas, sinon met à jour la note.
     */
    public function saveNotes(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'annee_scolaire'            => 'required|string',
            'niveau_id'                 => 'required|integer',
            'classe_id'                 => 'required|integer',
            'periode_type'               => 'required|in:trimestre,semestre',
            'periode_numero'             => 'required|integer',
            'notes'                      => 'required|array',
            'notes.*.eleve_id'           => 'required|integer',
            'notes.*.matiere_id'         => 'required|integer',
            'notes.*.type'               => 'required|in:cours,compo',
            'notes.*.mois'               => 'nullable|string',
            'notes.*.note'               => 'required|numeric|min:0',
        ]);

        // Plafond dynamique : Maternelle / Primaire -> /10, sinon -> /20
        $niveau = DB::table('tblniveau')
            ->where('i_niveauID', $request->niveau_id)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        abort_unless($niveau, 404, 'Niveau introuvable.');

        $nomNiveau = mb_strtolower($niveau->v_niveaux);
        $maxNote = (str_contains($nomNiveau, 'maternelle') || str_contains($nomNiveau, 'primaire')) ? 10 : 20;

        foreach ($request->notes as $n) {
            if ($n['note'] > $maxNote) {
                return response()->json([
                    'message' => "La note {$n['note']} dépasse le barème autorisé pour ce niveau (max {$maxNote})."
                ], 422);
            }
        }

        $userId = Auth::id();

        DB::transaction(function () use ($request, $ecole, $userId) {
            foreach ($request->notes as $n) {
                DB::table('tblnote')->updateOrInsert(
                    [
                        'ecole_id'       => $ecole->i_idecole,
                        'annee_scolaire' => $request->annee_scolaire,
                        'niveau_id'      => $request->niveau_id,
                        'classe_id'      => $request->classe_id,
                        'eleve_id'       => $n['eleve_id'],
                        'matiere_id'     => $n['matiere_id'],
                        'periode_type'   => $request->periode_type,
                        'periode_numero' => $request->periode_numero,
                        'type'           => $n['type'],
                        'mois'           => $n['mois'] ?? null,
                    ],
                    [
                        'note'       => $n['note'],
                        'created_by' => $userId,
                        'updated_at' => now(),
                    ]
                );
            }
        });

        return response()->json(['success' => true, 'message' => 'Notes enregistrées avec succès.']);
    }
}
