<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CaisseController extends Controller
{
    public function caisse($slug)
    {
        abort_unless(PermissionHelper::hasRoute('caisse'), 403);
        return view('ecoles.comptabilite.caisse', compact('slug'));
    }

    private function getEcoleId($slug)
{
    $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
    abort_if(!$ecole, 404, 'École introuvable');
    return $ecole->i_idecole;
}

    // Liste des caisses (AJAX)
    public function getCaisses($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $caisses = DB::table('tblcaisse')
            ->where('ecole_id', $ecoleId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $caisses]);
    }

    // Créer une nouvelle caisse
    public function store(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'nom'           => 'required|string|max:150',
            'description'   => 'nullable|string',
            'solde_initial' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $solde = $request->solde_initial ?? 0;
            $now = Carbon::now();

            $caisseId = DB::table('tblcaisse')->insertGetId([
                'ecole_id'    => $ecoleId,
                'nom'         => $request->nom,
                'description' => $request->description,
                'solde'       => $solde,
                'statut'      => 'active',
                'created_by'  => Auth::id(),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            if ($solde > 0) {
                DB::table('tblcaisse_mouvements')->insert([
                    'caisse_id'   => $caisseId,
                    'type'        => 'alimentation',
                    'montant'     => $solde,
                    'solde_apres' => $solde,
                    'motif'       => 'Solde initial à la création',
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            $caisse = DB::table('tblcaisse')->where('id', $caisseId)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Caisse créée avec succès', 'data' => $caisse]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Alimenter une caisse
    public function alimenter(Request $request, $slug)
    {
        $request->validate([
            'caisse_id' => 'required|exists:tblcaisse,id',
            'montant'   => 'required|numeric|min:0.01',
            'motif'     => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $caisse = DB::table('tblcaisse')->where('id', $request->caisse_id)->lockForUpdate()->first();

            if (!$caisse) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Caisse introuvable.'], 404);
            }

            if ($caisse->statut === 'suspendue') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Cette caisse est suspendue.'], 422);
            }

            $nouveauSolde = $caisse->solde + $request->montant;
            $now = Carbon::now();

            DB::table('tblcaisse')->where('id', $caisse->id)->update([
                'solde'      => $nouveauSolde,
                'updated_at' => $now,
            ]);

            DB::table('tblcaisse_mouvements')->insert([
                'caisse_id'   => $caisse->id,
                'type'        => 'alimentation',
                'montant'     => $request->montant,
                'solde_apres' => $nouveauSolde,
                'motif'       => $request->motif,
                'user_id'     => Auth::id(),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $caisseMaj = DB::table('tblcaisse')->where('id', $caisse->id)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Caisse alimentée avec succès', 'data' => $caisseMaj]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Transfert (interne entre caisses OU vers compte bancaire)
    public function transfert(Request $request, $slug)
    {
        $request->validate([
            'caisse_source_id' => 'required|exists:tblcaisse,id',
            'type_transfert'   => 'required|in:interne,bancaire',
            'caisse_dest_id'   => 'required_if:type_transfert,interne|nullable|exists:tblcaisse,id|different:caisse_source_id',
            'compte_dest_id'   => 'required_if:type_transfert,bancaire|nullable|exists:tblcomptebancaire,id',
            'montant'          => 'required|numeric|min:0.01',
            'motif'            => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $source = DB::table('tblcaisse')->where('id', $request->caisse_source_id)->lockForUpdate()->first();

            if (!$source) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Caisse source introuvable.'], 404);
            }
            if ($source->statut === 'suspendue') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'La caisse source est suspendue.'], 422);
            }
            if ($source->solde < $request->montant) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Solde insuffisant dans la caisse source.'], 422);
            }

            $now = Carbon::now();
            $soldeSourceApres = $source->solde - $request->montant;

            DB::table('tblcaisse')->where('id', $source->id)->update([
                'solde' => $soldeSourceApres, 'updated_at' => $now,
            ]);

            if ($request->type_transfert === 'interne') {
                // ---- Transfert caisse -> caisse (inchangé) ----
                $dest = DB::table('tblcaisse')->where('id', $request->caisse_dest_id)->lockForUpdate()->first();

                if (!$dest) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Caisse destination introuvable.'], 404);
                }
                if ($dest->statut === 'suspendue') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'La caisse destination est suspendue.'], 422);
                }

                $soldeDestApres = $dest->solde + $request->montant;

                DB::table('tblcaisse')->where('id', $dest->id)->update([
                    'solde' => $soldeDestApres, 'updated_at' => $now,
                ]);

                DB::table('tblcaisse_mouvements')->insert([
                    [
                        'caisse_id'             => $source->id,
                        'caisse_destination_id' => $dest->id,
                        'type'                  => 'transfert_sortant',
                        'montant'               => $request->montant,
                        'solde_apres'           => $soldeSourceApres,
                        'motif'                 => $request->motif,
                        'user_id'               => Auth::id(),
                        'created_at'            => $now,
                        'updated_at'            => $now,
                    ],
                    [
                        'caisse_id'             => $dest->id,
                        'caisse_destination_id' => $source->id,
                        'type'                  => 'transfert_entrant',
                        'montant'               => $request->montant,
                        'solde_apres'           => $soldeDestApres,
                        'motif'                 => $request->motif,
                        'user_id'               => Auth::id(),
                        'created_at'            => $now,
                        'updated_at'            => $now,
                    ],
                ]);
            } else {
                // ---- Transfert caisse -> compte bancaire (RÉEL désormais) ----
                $compte = DB::table('tblcomptebancaire')->where('id', $request->compte_dest_id)->lockForUpdate()->first();

                if (!$compte) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Compte bancaire introuvable.'], 404);
                }
                if ($compte->statut === 'suspendu') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Le compte bancaire est suspendu.'], 422);
                }

                $soldeCompteApres = $compte->solde + $request->montant;

                DB::table('tblcomptebancaire')->where('id', $compte->id)->update([
                    'solde' => $soldeCompteApres, 'updated_at' => $now,
                ]);

                // Mouvement côté caisse
                DB::table('tblcaisse_mouvements')->insert([
                    'caisse_id'          => $source->id,
                    'compte_bancaire_id' => $compte->id,
                    'type'               => 'transfert_vers_compte',
                    'montant'            => $request->montant,
                    'solde_apres'        => $soldeSourceApres,
                    'motif'              => $request->motif,
                    'banque_nom'         => $compte->banque,
                    'banque_compte'      => $compte->numero_compte,
                    'banque_iban'        => $compte->iban,
                    'user_id'            => Auth::id(),
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                // Mouvement côté compte bancaire (traçabilité croisée)
                DB::table('tblcomptebancaire_mouvements')->insert([
                    'compte_id'   => $compte->id,
                    'caisse_id'   => $source->id,
                    'type'        => 'depuis_caisse',
                    'montant'     => $request->montant,
                    'solde_apres' => $soldeCompteApres,
                    'motif'       => $request->motif,
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transfert effectué avec succès']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
    // Suspendre / réactiver une caisse
    public function suspendre($slug, $id)
    {
        try {
            $caisse = DB::table('tblcaisse')->where('id', $id)->first();

            if (!$caisse) {
                return response()->json(['success' => false, 'message' => 'Caisse introuvable.'], 404);
            }

            $nouveauStatut = $caisse->statut === 'active' ? 'suspendue' : 'active';

            DB::table('tblcaisse')->where('id', $id)->update([
                'statut'     => $nouveauStatut,
                'updated_at' => Carbon::now(),
            ]);

            $caisseMaj = DB::table('tblcaisse')->where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => $nouveauStatut === 'suspendue' ? 'Caisse suspendue' : 'Caisse réactivée',
                'data'    => $caisseMaj
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Détails / historique des mouvements d'une caisse (avec filtres)
    public function mouvements(Request $request, $slug, $id)
    {
        $caisse = DB::table('tblcaisse')->where('id', $id)->first();

        if (!$caisse) {
            return response()->json(['success' => false, 'message' => 'Caisse introuvable.'], 404);
        }

        $query = DB::table('tblcaisse_mouvements as m')
            ->leftJoin('tblcaisse as cd', 'cd.id', '=', 'm.caisse_destination_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id') // adaptez le nom de table/colonne si besoin
            ->where('m.caisse_id', $id)
            ->select(
                'm.*',
                'cd.nom as caisse_destination_nom',
                'u.name as user_nom' // adaptez 'name' si votre colonne s'appelle autrement
            );

        if ($request->filled('type')) {
            $query->where('m.type', $request->type);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('m.created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('m.created_at', '<=', $request->date_fin);
        }

        $mouvements = $query->orderBy('m.created_at', 'desc')->get();

        return response()->json(['success' => true, 'caisse' => $caisse, 'data' => $mouvements]);
    }
}
