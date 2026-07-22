<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PressanceController extends Controller
{
    public function pressance($slug)
    {
        abort_unless(PermissionHelper::hasRoute('pressance'), 403);
        return view('ecoles.pedagogie.pressance', compact('slug'));
    }

    private function getEcoleId($slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_if(!$ecole, 404, 'École introuvable');
        return $ecole->i_idecole;
    }

    // Liste des classes de l'école
    public function getClasses($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $classes = DB::table('tblclasse')
            ->where('i_ecole_id', $ecoleId)
            ->orderBy('v_nom_classe')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    }

    // Années scolaires disponibles (distinctes) pour l'école
    public function getAnnees($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $annees = DB::table('tblinscription')
            ->where('i_ecole_id', $ecoleId)
            ->distinct()
            ->orderBy('v_annee_scolaire', 'desc')
            ->pluck('v_annee_scolaire');

        return response()->json(['success' => true, 'data' => $annees]);
    }

    // Charger la liste des élèves inscrits d'une classe pour une date donnée (avec appel existant si déjà fait)
    public function getElevesAppel(Request $request, $slug)
    {
        $request->validate([
            'classe_id' => 'required|integer',
            'date'      => 'required|date',
            'annee'     => 'required|string',
        ]);

        $ecoleId = $this->getEcoleId($slug);
        $date = Carbon::parse($request->date);

        // Blocage dimanche (appel du lundi au samedi uniquement)
        if ($date->isSunday()) {
            return response()->json(['success' => false, 'message' => 'Aucun appel n\'est prévu le dimanche.'], 422);
        }

        $eleves = DB::table('tblinscription as ins')
            ->join('tbleleve as e', 'e.i_eleve_id', '=', 'ins.i_eleve_id')
            ->leftJoin('tblpresence as p', function ($join) use ($request) {
                $join->on('p.i_eleve_id', '=', 'ins.i_eleve_id')
                     ->where('p.i_classe_id', '=', $request->classe_id)
                     ->where('p.d_date_appel', '=', $request->date);
            })
            ->where('ins.i_ecole_id', $ecoleId)
            ->where('ins.i_classe_id', $request->classe_id)
            ->where('ins.v_annee_scolaire', $request->annee)
            ->where('ins.b_active', 1)
            ->where('ins.b_statut', 1)
            ->where('e.b_desabled', 1)
            ->select(
                'ins.i_inscription_id',
                'ins.i_eleve_id',
                'ins.i_niveau_id',
                'e.v_matricule',
                'e.v_nom',
                'e.v_prenom',
                'e.v_genre',
                'e.v_photo',
                'p.i_presence_id',
                DB::raw("COALESCE(p.v_statut, 'present') as v_statut"),
                'p.v_motif',
                'p.t_observation'
            )
            ->orderBy('e.v_nom')
            ->orderBy('e.v_prenom')
            ->get();

        return response()->json(['success' => true, 'data' => $eleves]);
    }

    // Enregistrer l'appel (upsert en masse)
    public function saveAppel(Request $request, $slug)
    {
        $request->validate([
            'classe_id'          => 'required|integer',
            'niveau_id'          => 'required|integer',
            'annee'              => 'required|string',
            'date'               => 'required|date',
            'appel'              => 'required|array|min:1',
            'appel.*.eleve_id'       => 'required|integer',
            'appel.*.inscription_id' => 'required|integer',
            'appel.*.statut'         => 'required|in:present,absent,retard,permission',
            'appel.*.motif'          => 'nullable|string|max:255',
            'appel.*.observation'    => 'nullable|string',
        ]);

        $ecoleId = $this->getEcoleId($slug);
        $date = Carbon::parse($request->date);

        if ($date->isSunday()) {
            return response()->json(['success' => false, 'message' => 'Aucun appel n\'est prévu le dimanche.'], 422);
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            foreach ($request->appel as $item) {
                $existe = DB::table('tblpresence')
                    ->where('i_eleve_id', $item['eleve_id'])
                    ->where('i_classe_id', $request->classe_id)
                    ->where('d_date_appel', $request->date)
                    ->first();

                $payload = [
                    'i_eleve_id'       => $item['eleve_id'],
                    'i_inscription_id' => $item['inscription_id'],
                    'i_classe_id'      => $request->classe_id,
                    'i_niveau_id'      => $request->niveau_id,
                    'i_ecole_id'       => $ecoleId,
                    'v_annee_scolaire' => $request->annee,
                    'd_date_appel'     => $request->date,
                    'v_statut'         => $item['statut'],
                    'v_motif'          => $item['motif'] ?? null,
                    't_observation'    => $item['observation'] ?? null,
                    'i_user_id'        => Auth::id(),
                    'd_updated_at'     => $now,
                ];

                if ($existe) {
                    DB::table('tblpresence')->where('i_presence_id', $existe->i_presence_id)->update($payload);
                } else {
                    $payload['d_created_at'] = $now;
                    DB::table('tblpresence')->insert($payload);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Appel enregistré avec succès']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Historique de l'appel d'une classe (avec filtres) pour consultation / export
    public function historique(Request $request, $slug)
    {
        $request->validate([
            'classe_id' => 'required|integer',
            'annee'     => 'required|string',
        ]);

        $ecoleId = $this->getEcoleId($slug);

        $query = DB::table('tblpresence as p')
            ->join('tbleleve as e', 'e.i_eleve_id', '=', 'p.i_eleve_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.i_user_id') // adaptez si besoin
            ->where('p.i_ecole_id', $ecoleId)
            ->where('p.i_classe_id', $request->classe_id)
            ->where('p.v_annee_scolaire', $request->annee)
            ->select(
                'p.*',
                'e.v_matricule', 'e.v_nom', 'e.v_prenom',
                'u.name as user_nom'
            );

        if ($request->filled('statut')) {
            $query->where('p.v_statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('p.d_date_appel', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('p.d_date_appel', '<=', $request->date_fin);
        }
        if ($request->filled('eleve_id')) {
            $query->where('p.i_eleve_id', $request->eleve_id);
        }

        $data = $query->orderBy('p.d_date_appel', 'desc')->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Statistiques rapides par élève sur une période (taux de présence)
    public function statsEleve(Request $request, $slug)
    {
        $request->validate([
            'classe_id' => 'required|integer',
            'annee'     => 'required|string',
        ]);

        $ecoleId = $this->getEcoleId($slug);

        $stats = DB::table('tblpresence as p')
            ->join('tbleleve as e', 'e.i_eleve_id', '=', 'p.i_eleve_id')
            ->where('p.i_ecole_id', $ecoleId)
            ->where('p.i_classe_id', $request->classe_id)
            ->where('p.v_annee_scolaire', $request->annee)
            ->select(
                'p.i_eleve_id',
                DB::raw("CONCAT(e.v_nom, ' ', e.v_prenom) as nom_complet"),
                DB::raw("SUM(CASE WHEN p.v_statut = 'present' THEN 1 ELSE 0 END) as nb_present"),
                DB::raw("SUM(CASE WHEN p.v_statut = 'absent' THEN 1 ELSE 0 END) as nb_absent"),
                DB::raw("SUM(CASE WHEN p.v_statut = 'retard' THEN 1 ELSE 0 END) as nb_retard"),
                DB::raw("SUM(CASE WHEN p.v_statut = 'permission' THEN 1 ELSE 0 END) as nb_permission"),
                DB::raw("COUNT(*) as total_appels")
            )
            ->groupBy('p.i_eleve_id', 'e.v_nom', 'e.v_prenom')
            ->orderBy('e.v_nom')
            ->get();

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
