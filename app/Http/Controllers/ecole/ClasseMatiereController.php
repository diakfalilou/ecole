<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClasseMatiereController extends Controller
{
    public function classeMatiere($slug)
    {
        abort_unless(PermissionHelper::hasRoute('classe-matiere'), 403);
        return view('ecoles.matieres.classe-matiere', compact('slug'));
    }

    private function getEcoleId($slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_if(!$ecole, 404, 'École introuvable pour ce slug : ' . $slug);
        return $ecole->i_idecole;
    }

    // Liste des classes de l'école (pour le select)
    public function getClasses($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $classes = DB::table('tblclasse')
            ->where('i_ecole_id', $ecoleId)
            ->orderBy('v_nom_classe')
            ->get();

        return response()->json(['success' => true, 'data' => $classes]);
    }

    // Charger toutes les matières actives + indiquer lesquelles sont déjà affectées à la classe donnée
    public function getMatieresPourClasse(Request $request, $slug)
    {
        $request->validate(['classe_id' => 'required|integer']);
        $ecoleId = $this->getEcoleId($slug);

        $matieres = DB::table('tblmatiere as m')
            ->leftJoin('tblclassematiere as cm', function ($join) use ($request) {
                $join->on('cm.matiere_id', '=', 'm.id')
                     ->where('cm.classe_id', '=', $request->classe_id);
            })
            ->where('m.ecole_id', $ecoleId)
            ->where('m.statut', 'active')
            ->select(
                'm.id as matiere_id',
                'm.code',
                'm.nom',
                'cm.id as affectation_id',

            )
            ->orderBy('m.nom')
            ->get();

        return response()->json(['success' => true, 'data' => $matieres]);
    }

    // Enregistrer les affectations (bulk upsert + suppression de celles décochées)
    public function enregistrerAffectations(Request $request, $slug)
    {
        $request->validate([
            'classe_id'                  => 'required|integer',
            'affectations'                => 'array',
            'affectations.*.matiere_id'   => 'required|integer',
            'affectations.*.coefficient'  => 'required|numeric|min:0.01|max:99.99',
            'affectations.*.volume_horaire' => 'nullable|integer|min:0',
        ]);

        $ecoleId = $this->getEcoleId($slug);

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $matieresCochees = collect($request->affectations)->pluck('matiere_id')->toArray();

            // Supprimer les affectations qui ne sont plus cochées
            DB::table('tblclassematiere')
                ->where('classe_id', $request->classe_id)
                ->whereNotIn('matiere_id', $matieresCochees)
                ->delete();

            // Upsert de chaque matière cochée
            foreach ($request->affectations as $item) {
                $existe = DB::table('tblclassematiere')
                    ->where('classe_id', $request->classe_id)
                    ->where('matiere_id', $item['matiere_id'])
                    ->first();

                $payload = [
                    'ecole_id'       => $ecoleId,
                    'classe_id'      => $request->classe_id,
                    'matiere_id'     => $item['matiere_id'],
                    'coefficient'    => $item['coefficient'],
                    'volume_horaire' => $item['volume_horaire'] ?? null,
                    'updated_at'     => $now,
                ];

                if ($existe) {
                    DB::table('tblclassematiere')->where('id', $existe->id)->update($payload);
                } else {
                    $payload['created_by'] = Auth::id();
                    $payload['created_at'] = $now;
                    DB::table('tblclassematiere')->insert($payload);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Affectation des matières enregistrée avec succès']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Vue récapitulative : toutes les classes avec le nombre de matières affectées (aperçu global)
    public function recapitulatif($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $recap = DB::table('tblclasse as c')
            ->leftJoin('tblclassematiere as cm', 'cm.classe_id', '=', 'c.i_classe_id')
            ->where('c.i_ecole_id', $ecoleId)
            ->select('c.i_classe_id', 'c.v_nom_classe', DB::raw('COUNT(cm.id) as nb_matieres'), DB::raw('COALESCE(SUM(cm.coefficient), 0) as total_coefficient'))
            ->groupBy('c.i_classe_id', 'c.v_nom_classe')
            ->orderBy('c.v_nom_classe')
            ->get();

        return response()->json(['success' => true, 'data' => $recap]);
    }
}
