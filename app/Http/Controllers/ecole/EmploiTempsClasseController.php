<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmploiTempsClasseController extends Controller
{
    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    public function index($slug)
    {
        abort_unless(PermissionHelper::hasRoute('emploi-du-temps'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblcontrat')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_contrat_id', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annee_scolaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.emploi-du-temps.index', compact('slug', 'niveaux', 'data_anneescolaire', 'annee_courante', 'ecole'));
    }

    public function getClassesByNiveau($slug, $niveauId)
    {
        $ecole = $this->getEcole($slug);

        $classes = DB::table('tblclasse')
            ->where('i_niveau_id', $niveauId)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_desabled', 1)
            ->select('i_classe_id', 'v_nom_classe')
            ->orderBy('v_nom_classe')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    }

    // Matières affectées à la classe pour l'année scolaire (issues de tblclassematiere)
    public function getMatieresPourClasse(Request $request, $slug)
    {
        $request->validate([
            'classe_id'      => 'required|integer',
            'annee_scolaire' => 'required|string',
        ]);

        $ecole = $this->getEcole($slug);

        $matieres = DB::table('tblclassematiere as cm')
            ->join('tblmatiere as m', 'm.id', '=', 'cm.matiere_id')
            ->where('cm.classe_id', $request->classe_id)
            ->where('cm.annee_scolaire', $request->annee_scolaire)
            ->where('cm.ecole_id', $ecole->i_idecole)
            ->select('m.id', 'm.nom', 'm.code', 'cm.coefficient')
            ->orderBy('m.nom')
            ->get();

        return response()->json(['success' => true, 'data' => $matieres]);
    }

    // Professeurs actifs de l'école
    public function getProfesseurs($slug)
    {
        $ecole = $this->getEcole($slug);

        $professeurs = DB::table('tblproffesseur')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('statut', 'active')
            ->select('id', 'nom', 'prenom', 'matricule', 'specialite')
            ->orderBy('nom')
            ->get();

        return response()->json(['success' => true, 'data' => $professeurs]);
    }

    // Emploi du temps existant pour une classe + une année scolaire
    public function getEmploiDuTemps(Request $request, $slug)
    {
        $request->validate([
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'annee_scolaire' => 'required|string',
        ]);

        $ecole = $this->getEcole($slug);

        $creneaux = DB::table('tblemploitempsclasse as e')
            ->join('tblmatiere as m', 'm.id', '=', 'e.matiere_id')
            ->leftJoin('tblproffesseur as p', 'p.id', '=', 'e.professeur_id')
            ->where('e.ecole_id', $ecole->i_idecole)
            ->where('e.classe_id', $request->classe_id)
            ->where('e.annee_scolaire', $request->annee_scolaire)
            ->select(
                'e.id',
                'e.jour',
                'e.heure_debut',
                'e.heure_fin',
                'e.salle',
                'e.matiere_id',
                'm.nom as matiere_nom',
                'e.professeur_id',
                'p.nom as professeur_nom',
                'p.prenom as professeur_prenom'
            )
            ->orderByRaw("FIELD(e.jour, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi')")
            ->orderBy('e.heure_debut')
            ->get();

        return response()->json(['success' => true, 'data' => $creneaux]);
    }

    // Créer ou modifier un créneau
    public function saveCreneau(Request $request, $slug)
    {
        $request->validate([
            'id'             => 'nullable|integer',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'annee_scolaire' => 'required|string',
            'jour'           => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'heure_debut'    => 'required|date_format:H:i',
            'heure_fin'      => 'required|date_format:H:i|after:heure_debut',
            'matiere_id'     => 'required|integer',
            'professeur_id'  => 'nullable|integer',
            'salle'          => 'nullable|string|max:100',
        ]);

        $ecole = $this->getEcole($slug);
        $userId = Auth::id();

        // Conflit dans la même classe (chevauchement horaire le même jour)
        $conflitClasse = DB::table('tblemploitempsclasse')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('classe_id', $request->classe_id)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('jour', $request->jour)
            ->where('heure_debut', '<', $request->heure_fin)
            ->where('heure_fin', '>', $request->heure_debut)
            ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
            ->exists();

        if ($conflitClasse) {
            return response()->json(['success' => false, 'message' => 'Un créneau existe déjà pour cette classe sur ce créneau horaire.'], 422);
        }

        // Conflit professeur (déjà occupé sur une autre classe au même moment)
        if ($request->professeur_id) {
            $conflitProf = DB::table('tblemploitempsclasse')
                ->where('ecole_id', $ecole->i_idecole)
                ->where('professeur_id', $request->professeur_id)
                ->where('annee_scolaire', $request->annee_scolaire)
                ->where('jour', $request->jour)
                ->where('heure_debut', '<', $request->heure_fin)
                ->where('heure_fin', '>', $request->heure_debut)
                ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
                ->exists();

            if ($conflitProf) {
                return response()->json(['success' => false, 'message' => 'Ce professeur est déjà affecté à un autre cours sur ce créneau horaire.'], 422);
            }
        }

        $payload = [
            'ecole_id'       => $ecole->i_idecole,
            'annee_scolaire' => $request->annee_scolaire,
            'niveau_id'      => $request->niveau_id,
            'classe_id'      => $request->classe_id,
            'jour'           => $request->jour,
            'heure_debut'    => $request->heure_debut,
            'heure_fin'      => $request->heure_fin,
            'matiere_id'     => $request->matiere_id,
            'professeur_id'  => $request->professeur_id,
            'salle'          => $request->salle,
            'updated_at'     => now(),
        ];

        if ($request->id) {
            DB::table('tblemploitempsclasse')
                ->where('id', $request->id)
                ->where('ecole_id', $ecole->i_idecole)
                ->update($payload);

            return response()->json(['success' => true, 'message' => 'Créneau modifié avec succès.']);
        }

        $payload['created_by'] = $userId;
        $payload['created_at'] = now();

        DB::table('tblemploitempsclasse')->insert($payload);

        return response()->json(['success' => true, 'message' => 'Créneau ajouté avec succès.']);
    }

    // Supprimer un créneau
    public function deleteCreneau($slug, $id)
    {
        $ecole = $this->getEcole($slug);

        $deleted = DB::table('tblemploitempsclasse')
            ->where('id', $id)
            ->where('ecole_id', $ecole->i_idecole)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Créneau introuvable.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Créneau supprimé avec succès.']);
    }
}
