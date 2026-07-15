<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MatiereController extends Controller
{
    public function matiere($slug)
    {
        abort_unless(PermissionHelper::hasRoute('matiere'), 403);
        return view('ecoles.matieres.index', compact('slug'));
    }

    private function getEcoleId($slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_if(!$ecole, 404, 'École introuvable pour ce slug : ' . $slug);
        return $ecole->i_idecole;
    }

    // Liste des matières (AJAX, avec filtres)
    public function getMatieres(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $query = DB::table('tblmatiere')
            ->where('ecole_id', $ecoleId);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('recherche')) {
            $r = $request->recherche;
            $query->where(function ($q) use ($r) {
                $q->where('nom', 'like', "%{$r}%")
                  ->orWhere('code', 'like', "%{$r}%");
            });
        }

        $matieres = $query->orderBy('nom')->get();

        return response()->json(['success' => true, 'data' => $matieres]);
    }

    // Détail d'une matière
    public function detail($slug, $id)
    {
        $ecoleId = $this->getEcoleId($slug);

        $matiere = DB::table('tblmatiere')
            ->where('id', $id)
            ->where('ecole_id', $ecoleId)
            ->first();

        if (!$matiere) {
            return response()->json(['success' => false, 'message' => 'Matière introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $matiere]);
    }

    // Créer une matière
    public function store(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'nom'         => 'required|string|max:150',
            'code'        => 'nullable|string|max:30',
            'description' => 'nullable|string',
        ]);

        if ($request->filled('code')) {
            $existe = DB::table('tblmatiere')
                ->where('ecole_id', $ecoleId)
                ->where('code', $request->code)
                ->exists();

            if ($existe) {
                return response()->json(['success' => false, 'message' => 'Ce code de matière est déjà utilisé.'], 422);
            }
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            $id = DB::table('tblmatiere')->insertGetId([
                'ecole_id'    => $ecoleId,
                'code'        => $request->code,
                'nom'         => $request->nom,
                'description' => $request->description,
                'statut'      => 'active',
                'created_by'  => Auth::id(),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $matiere = DB::table('tblmatiere')->where('id', $id)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Matière ajoutée avec succès', 'data' => $matiere]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Modifier une matière
    public function update(Request $request, $slug, $id)
    {
        $ecoleId = $this->getEcoleId($slug);

        $matiere = DB::table('tblmatiere')->where('id', $id)->where('ecole_id', $ecoleId)->first();
        if (!$matiere) {
            return response()->json(['success' => false, 'message' => 'Matière introuvable.'], 404);
        }

        $request->validate([
            'nom'         => 'required|string|max:150',
            'code'        => 'nullable|string|max:30',
            'description' => 'nullable|string',
        ]);

        if ($request->filled('code')) {
            $existe = DB::table('tblmatiere')
                ->where('ecole_id', $ecoleId)
                ->where('code', $request->code)
                ->where('id', '!=', $id)
                ->exists();

            if ($existe) {
                return response()->json(['success' => false, 'message' => 'Ce code de matière est déjà utilisé.'], 422);
            }
        }

        DB::beginTransaction();
        try {
            DB::table('tblmatiere')->where('id', $id)->update([
                'code'        => $request->code,
                'nom'         => $request->nom,
                'description' => $request->description,
                'updated_at'  => Carbon::now(),
            ]);

            $matiereMaj = DB::table('tblmatiere')->where('id', $id)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Matière modifiée avec succès', 'data' => $matiereMaj]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Suspendre / réactiver une matière
    public function suspendre(Request $request, $slug, $id)
    {
        $request->validate(['motif' => 'nullable|string|max:255']);

        try {
            $matiere = DB::table('tblmatiere')->where('id', $id)->first();
            if (!$matiere) {
                return response()->json(['success' => false, 'message' => 'Matière introuvable.'], 404);
            }

            $nouveauStatut = $matiere->statut === 'active' ? 'suspendue' : 'active';

            DB::table('tblmatiere')->where('id', $id)->update([
                'statut'           => $nouveauStatut,
                'motif_suspension' => $nouveauStatut === 'suspendue' ? $request->motif : null,
                'updated_at'       => Carbon::now(),
            ]);

            $matiereMaj = DB::table('tblmatiere')->where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => $nouveauStatut === 'suspendue' ? 'Matière suspendue' : 'Matière réactivée',
                'data'    => $matiereMaj
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
