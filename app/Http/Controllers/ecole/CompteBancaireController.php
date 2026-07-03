<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CompteBancaireController extends Controller
{
    public function compteBancaire($slug)
    {
        abort_unless(PermissionHelper::hasRoute('compte-bancaire'), 403);
        return view('ecoles.comptabilite.compte_bancaire', compact('slug'));
    }

   private function getEcoleId($slug)
{
    $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
    abort_if(!$ecole, 404, 'École introuvable');
    return $ecole->i_idecole;
}

    // Liste des comptes bancaires (AJAX)
    public function getComptes($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $comptes = DB::table('tblcomptebancaire')
            ->where('ecole_id', $ecoleId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $comptes]);
    }

    // Créer un compte bancaire
    public function store(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'nom'           => 'required|string|max:150',
            'banque'        => 'required|string|max:150',
            'numero_compte' => 'required|string|max:100',
            'iban'          => 'nullable|string|max:100',
            'solde_initial' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $solde = $request->solde_initial ?? 0;
            $now = Carbon::now();

            $compteId = DB::table('tblcomptebancaire')->insertGetId([
                'ecole_id'      => $ecoleId,
                'nom'           => $request->nom,
                'banque'        => $request->banque,
                'numero_compte' => $request->numero_compte,
                'iban'          => $request->iban,
                'solde'         => $solde,
                'statut'        => 'active',
                'created_by'    => Auth::id(),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            if ($solde > 0) {
                DB::table('tblcomptebancaire_mouvements')->insert([
                    'compte_id'   => $compteId,
                    'type'        => 'alimentation',
                    'montant'     => $solde,
                    'solde_apres' => $solde,
                    'motif'       => 'Solde initial à la création',
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            $compte = DB::table('tblcomptebancaire')->where('id', $compteId)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Compte bancaire créé avec succès', 'data' => $compte]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Alimenter un compte bancaire
    public function alimenter(Request $request, $slug)
    {
        $request->validate([
            'compte_id' => 'required|exists:tblcomptebancaire,id',
            'montant'   => 'required|numeric|min:0.01',
            'motif'     => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $compte = DB::table('tblcomptebancaire')->where('id', $request->compte_id)->lockForUpdate()->first();

            if (!$compte) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Compte introuvable.'], 404);
            }
            if ($compte->statut === 'suspendu') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Ce compte est suspendu.'], 422);
            }

            $nouveauSolde = $compte->solde + $request->montant;
            $now = Carbon::now();

            DB::table('tblcomptebancaire')->where('id', $compte->id)->update([
                'solde' => $nouveauSolde, 'updated_at' => $now,
            ]);

            DB::table('tblcomptebancaire_mouvements')->insert([
                'compte_id'   => $compte->id,
                'type'        => 'alimentation',
                'montant'     => $request->montant,
                'solde_apres' => $nouveauSolde,
                'motif'       => $request->motif,
                'user_id'     => Auth::id(),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $compteMaj = DB::table('tblcomptebancaire')->where('id', $compte->id)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Compte alimenté avec succès', 'data' => $compteMaj]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Transfert : compte -> compte OU compte -> caisse
    public function transfert(Request $request, $slug)
    {
        $request->validate([
            'compte_source_id' => 'required|exists:tblcomptebancaire,id',
            'type_transfert'   => 'required|in:compte,caisse',
            'compte_dest_id'   => 'required_if:type_transfert,compte|nullable|exists:tblcomptebancaire,id|different:compte_source_id',
            'caisse_dest_id'   => 'required_if:type_transfert,caisse|nullable|exists:tblcaisse,id',
            'montant'          => 'required|numeric|min:0.01',
            'motif'            => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $source = DB::table('tblcomptebancaire')->where('id', $request->compte_source_id)->lockForUpdate()->first();

            if (!$source) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Compte source introuvable.'], 404);
            }
            if ($source->statut === 'suspendu') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Le compte source est suspendu.'], 422);
            }
            if ($source->solde < $request->montant) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Solde insuffisant sur le compte source.'], 422);
            }

            $now = Carbon::now();
            $soldeSourceApres = $source->solde - $request->montant;

            DB::table('tblcomptebancaire')->where('id', $source->id)->update([
                'solde' => $soldeSourceApres, 'updated_at' => $now,
            ]);

            if ($request->type_transfert === 'compte') {
                // ---- Transfert compte -> compte ----
                $dest = DB::table('tblcomptebancaire')->where('id', $request->compte_dest_id)->lockForUpdate()->first();

                if (!$dest) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Compte destination introuvable.'], 404);
                }
                if ($dest->statut === 'suspendu') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Le compte destination est suspendu.'], 422);
                }

                $soldeDestApres = $dest->solde + $request->montant;

                DB::table('tblcomptebancaire')->where('id', $dest->id)->update([
                    'solde' => $soldeDestApres, 'updated_at' => $now,
                ]);

                DB::table('tblcomptebancaire_mouvements')->insert([
                    [
                        'compte_id'             => $source->id,
                        'compte_destination_id' => $dest->id,
                        'type'                  => 'transfert_sortant',
                        'montant'               => $request->montant,
                        'solde_apres'           => $soldeSourceApres,
                        'motif'                 => $request->motif,
                        'user_id'               => Auth::id(),
                        'created_at'            => $now,
                        'updated_at'            => $now,
                    ],
                    [
                        'compte_id'             => $dest->id,
                        'compte_destination_id' => $source->id,
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
                // ---- Transfert compte -> caisse ----
                $caisse = DB::table('tblcaisse')->where('id', $request->caisse_dest_id)->lockForUpdate()->first();

                if (!$caisse) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Caisse destination introuvable.'], 404);
                }
                if ($caisse->statut === 'suspendue') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'La caisse destination est suspendue.'], 422);
                }

                $soldeCaisseApres = $caisse->solde + $request->montant;

                DB::table('tblcaisse')->where('id', $caisse->id)->update([
                    'solde' => $soldeCaisseApres, 'updated_at' => $now,
                ]);

                // Mouvement côté compte bancaire
                DB::table('tblcomptebancaire_mouvements')->insert([
                    'compte_id'   => $source->id,
                    'caisse_id'   => $caisse->id,
                    'type'        => 'vers_caisse',
                    'montant'     => $request->montant,
                    'solde_apres' => $soldeSourceApres,
                    'motif'       => $request->motif,
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                // Mouvement côté caisse (traçabilité croisée)
                DB::table('tblcaisse_mouvements')->insert([
                    'caisse_id'         => $caisse->id,
                    'compte_bancaire_id'=> $source->id,
                    'type'              => 'transfert_depuis_compte',
                    'montant'           => $request->montant,
                    'solde_apres'       => $soldeCaisseApres,
                    'motif'             => $request->motif,
                    'banque_nom'        => $source->banque,
                    'banque_compte'     => $source->numero_compte,
                    'banque_iban'       => $source->iban,
                    'user_id'           => Auth::id(),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transfert effectué avec succès']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Suspendre / réactiver un compte
    public function suspendre($slug, $id)
    {
        try {
            $compte = DB::table('tblcomptebancaire')->where('id', $id)->first();
            if (!$compte) {
                return response()->json(['success' => false, 'message' => 'Compte introuvable.'], 404);
            }

            $nouveauStatut = $compte->statut === 'active' ? 'suspendu' : 'active';

            DB::table('tblcomptebancaire')->where('id', $id)->update([
                'statut' => $nouveauStatut, 'updated_at' => Carbon::now(),
            ]);

            $compteMaj = DB::table('tblcomptebancaire')->where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => $nouveauStatut === 'suspendu' ? 'Compte suspendu' : 'Compte réactivé',
                'data'    => $compteMaj
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Historique des mouvements d'un compte (avec filtres)
    public function mouvements(Request $request, $slug, $id)
    {
        $compte = DB::table('tblcomptebancaire')->where('id', $id)->first();
        if (!$compte) {
            return response()->json(['success' => false, 'message' => 'Compte introuvable.'], 404);
        }

        $query = DB::table('tblcomptebancaire_mouvements as m')
            ->leftJoin('tblcomptebancaire as cd', 'cd.id', '=', 'm.compte_destination_id')
            ->leftJoin('tblcaisse as ca', 'ca.id', '=', 'm.caisse_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
            ->where('m.compte_id', $id)
            ->select('m.*', 'cd.nom as compte_destination_nom', 'ca.nom as caisse_nom', 'u.name as user_nom');

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

        return response()->json(['success' => true, 'compte' => $compte, 'data' => $mouvements]);
    }
}
