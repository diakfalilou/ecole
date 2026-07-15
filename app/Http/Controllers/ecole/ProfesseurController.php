<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfesseurController extends Controller
{
    public function professeur($slug)
    {
        abort_unless(PermissionHelper::hasRoute('professeur'), 403);
        return view('ecoles.proffesseurs.index', compact('slug'));
    }

    private function getEcoleId($slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_if(!$ecole, 404, 'École introuvable pour ce slug : ' . $slug);
        return $ecole->i_idecole;
    }

    // Liste des professeurs (AJAX, avec filtres)
    public function getProfesseurs(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $query = DB::table('tblproffesseur')
            ->where('ecole_id', $ecoleId);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('specialite')) {
            $query->where('specialite', $request->specialite);
        }
        if ($request->filled('recherche')) {
            $r = $request->recherche;
            $query->where(function ($q) use ($r) {
                $q->where('nom', 'like', "%{$r}%")
                  ->orWhere('prenom', 'like', "%{$r}%")
                  ->orWhere('matricule', 'like', "%{$r}%")
                  ->orWhere('telephone', 'like', "%{$r}%");
            });
        }

        $professeurs = $query->orderBy('nom')->orderBy('prenom')->get();

        return response()->json(['success' => true, 'data' => $professeurs]);
    }

    // Détail d'un professeur
    public function detail($slug, $id)
    {
        $ecoleId = $this->getEcoleId($slug);

        $professeur = DB::table('tblproffesseur')
            ->where('id', $id)
            ->where('ecole_id', $ecoleId)
            ->first();

        if (!$professeur) {
            return response()->json(['success' => false, 'message' => 'Professeur introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $professeur]);
    }

    // Générer automatiquement un matricule si non fourni
    private function genererMatricule($ecoleId)
    {
        $annee = date('Y');
        $dernier = DB::table('tblproffesseur')
            ->where('ecole_id', $ecoleId)
            ->where('matricule', 'like', "PROF-{$annee}-%")
            ->orderByDesc('id')
            ->value('matricule');

        $numero = 1;
        if ($dernier) {
            $parts = explode('-', $dernier);
            $numero = intval(end($parts)) + 1;
        }

        return "PROF-{$annee}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    // Créer un professeur
    public function store(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'nom'            => 'required|string|max:150',
            'prenom'         => 'required|string|max:150',
            'genre'          => 'nullable|in:Masculin,Féminin',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:150',
            'telephone'      => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'adresse'        => 'nullable|string',
            'specialite'     => 'nullable|string|max:150',
            'diplome'        => 'nullable|string|max:150',
            'date_embauche'  => 'nullable|date',
            'type_contrat'   => 'required|in:permanent,vacataire',
            'salaire_base'   => 'nullable|numeric|min:0',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            $chemin = null;
            if ($request->hasFile('photo')) {
                $chemin = $request->file('photo')->store('professeurs/photos', 'public');
            }

            $matricule = $this->genererMatricule($ecoleId);

            $id = DB::table('tblproffesseur')->insertGetId([
                'ecole_id'         => $ecoleId,
                'matricule'        => $matricule,
                'nom'              => $request->nom,
                'prenom'           => $request->prenom,
                'genre'            => $request->genre,
                'date_naissance'   => $request->date_naissance,
                'lieu_naissance'   => $request->lieu_naissance,
                'telephone'        => $request->telephone,
                'email'            => $request->email,
                'adresse'          => $request->adresse,
                'photo'            => $chemin,
                'specialite'       => $request->specialite,
                'diplome'          => $request->diplome,
                'date_embauche'    => $request->date_embauche,
                'type_contrat'     => $request->type_contrat,
                'salaire_base'     => $request->salaire_base,
                'statut'           => 'active',
                'created_by'       => Auth::id(),
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            $professeur = DB::table('tblproffesseur')->where('id', $id)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Professeur ajouté avec succès', 'data' => $professeur]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Modifier un professeur
    public function update(Request $request, $slug, $id)
    {
        $ecoleId = $this->getEcoleId($slug);

        $professeur = DB::table('tblproffesseur')->where('id', $id)->where('ecole_id', $ecoleId)->first();
        if (!$professeur) {
            return response()->json(['success' => false, 'message' => 'Professeur introuvable.'], 404);
        }

        $request->validate([
            'nom'            => 'required|string|max:150',
            'prenom'         => 'required|string|max:150',
            'genre'          => 'nullable|in:Masculin,Féminin',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:150',
            'telephone'      => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'adresse'        => 'nullable|string',
            'specialite'     => 'nullable|string|max:150',
            'diplome'        => 'nullable|string|max:150',
            'date_embauche'  => 'nullable|date',
            'type_contrat'   => 'required|in:permanent,vacataire',
            'salaire_base'   => 'nullable|numeric|min:0',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            $payload = [
                'nom'            => $request->nom,
                'prenom'         => $request->prenom,
                'genre'          => $request->genre,
                'date_naissance' => $request->date_naissance,
                'lieu_naissance' => $request->lieu_naissance,
                'telephone'      => $request->telephone,
                'email'          => $request->email,
                'adresse'        => $request->adresse,
                'specialite'     => $request->specialite,
                'diplome'        => $request->diplome,
                'date_embauche'  => $request->date_embauche,
                'type_contrat'   => $request->type_contrat,
                'salaire_base'   => $request->salaire_base,
                'updated_at'     => $now,
            ];

            if ($request->hasFile('photo')) {
                $payload['photo'] = $request->file('photo')->store('professeurs/photos', 'public');
            }

            DB::table('tblproffesseur')->where('id', $id)->update($payload);

            $professeurMaj = DB::table('tblproffesseur')->where('id', $id)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Professeur modifié avec succès', 'data' => $professeurMaj]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Suspendre / réactiver un professeur
    public function suspendre(Request $request, $slug, $id)
    {
        $request->validate([
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            $professeur = DB::table('tblproffesseur')->where('id', $id)->first();
            if (!$professeur) {
                return response()->json(['success' => false, 'message' => 'Professeur introuvable.'], 404);
            }

            $nouveauStatut = $professeur->statut === 'active' ? 'suspendu' : 'active';

            DB::table('tblproffesseur')->where('id', $id)->update([
                'statut'           => $nouveauStatut,
                'motif_suspension' => $nouveauStatut === 'suspendu' ? $request->motif : null,
                'updated_at'       => Carbon::now(),
            ]);

            $professeurMaj = DB::table('tblproffesseur')->where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => $nouveauStatut === 'suspendu' ? 'Professeur suspendu' : 'Professeur réactivé',
                'data'    => $professeurMaj
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
