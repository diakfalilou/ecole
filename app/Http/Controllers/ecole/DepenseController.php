<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DepenseController extends Controller
{
    public function depense($slug)
    {
        abort_unless(PermissionHelper::hasRoute('depense'), 403);
        return view('ecoles.comptabilite.depense', compact('slug'));
    }

    private function getEcoleId($slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_if(!$ecole, 404, 'École introuvable pour ce slug : ' . $slug);
        return $ecole->i_idecole;
    }

    // Caisses + comptes bancaires actifs disponibles comme source de paiement
    public function getSources($slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $caisses = DB::table('tblcaisse')
            ->where('ecole_id', $ecoleId)
            ->where('statut', 'active')
            ->select('id', 'nom', 'solde', DB::raw("'caisse' as type"))
            ->orderBy('nom')
            ->get();

        $comptes = DB::table('tblcomptebancaire')
            ->where('ecole_id', $ecoleId)
            ->where('statut', 'active')
            ->select('id', 'nom', 'banque', 'numero_compte', 'solde', DB::raw("'compte_bancaire' as type"))
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success'    => true,
            'ecole_id'   => $ecoleId, // utile pour debug, à retirer en prod si besoin
            'caisses'    => $caisses,
            'comptes'    => $comptes,
            'nb_caisses' => $caisses->count(),
            'nb_comptes' => $comptes->count(),
        ]);
    }

    // Liste des dépenses (AJAX, avec filtres)
    public function getDepenses(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $query = DB::table('tbldepense as d')
            ->leftJoin('tblcaisse as c', function ($j) {
                $j->on('c.id', '=', 'd.source_id')->where('d.source_type', '=', 'caisse');
            })
            ->leftJoin('tblcomptebancaire as cb', function ($j) {
                $j->on('cb.id', '=', 'd.source_id')->where('d.source_type', '=', 'compte_bancaire');
            })
            ->leftJoin('users as u', 'u.id', '=', 'd.user_id')
            ->where('d.ecole_id', $ecoleId)
            ->select(
                'd.*',
                'c.nom as caisse_nom',
                'cb.nom as compte_nom',
                'cb.banque as compte_banque',
                'u.name as user_nom'
            );

        if ($request->filled('categorie')) {
            $query->where('d.categorie', $request->categorie);
        }
        if ($request->filled('source_type')) {
            $query->where('d.source_type', $request->source_type);
        }
        if ($request->filled('statut')) {
            $query->where('d.statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('d.date_depense', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('d.date_depense', '<=', $request->date_fin);
        }

        $depenses = $query->orderBy('d.date_depense', 'desc')->orderBy('d.id', 'desc')->get();

        return response()->json(['success' => true, 'data' => $depenses]);
    }

    // Créer une dépense (retire l'argent de la source + trace le mouvement)
    public function store(Request $request, $slug)
    {
        $ecoleId = $this->getEcoleId($slug);

        $request->validate([
            'categorie'    => 'required|string|max:150',
            'libelle'      => 'required|string|max:255',
            'description'  => 'nullable|string',
            'montant'      => 'required|numeric|min:0.01',
            'source_type'  => 'required|in:caisse,compte_bancaire',
            'source_id'    => 'required|integer',
            'beneficiaire' => 'nullable|string|max:150',
            'date_depense' => 'required|date',
            'piece'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $caisseMouvementId = null;
            $compteMouvementId = null;

            if ($request->source_type === 'caisse') {
                $caisse = DB::table('tblcaisse')
                    ->where('id', $request->source_id)
                    ->where('ecole_id', $ecoleId)
                    ->lockForUpdate()
                    ->first();

                if (!$caisse) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Caisse introuvable.'], 404);
                }
                if ($caisse->statut === 'suspendue') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Cette caisse est suspendue.'], 422);
                }
                if ($caisse->solde < $request->montant) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Solde insuffisant dans la caisse « {$caisse->nom} ». Solde disponible : " . number_format($caisse->solde, 2, ',', ' ')], 422);
                }

                $soldeApres = $caisse->solde - $request->montant;

                DB::table('tblcaisse')->where('id', $caisse->id)->update(['solde' => $soldeApres, 'updated_at' => $now]);

                $caisseMouvementId = DB::table('tblcaisse_mouvements')->insertGetId([
                    'caisse_id'   => $caisse->id,
                    'type'        => 'depense',
                    'montant'     => $request->montant,
                    'solde_apres' => $soldeApres,
                    'motif'       => $request->libelle,
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            } else {
                $compte = DB::table('tblcomptebancaire')
                    ->where('id', $request->source_id)
                    ->where('ecole_id', $ecoleId)
                    ->lockForUpdate()
                    ->first();

                if (!$compte) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Compte bancaire introuvable.'], 404);
                }
                if ($compte->statut === 'suspendu') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Ce compte est suspendu.'], 422);
                }
                if ($compte->solde < $request->montant) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Solde insuffisant sur le compte « {$compte->nom} ». Solde disponible : " . number_format($compte->solde, 2, ',', ' ')], 422);
                }

                $soldeApres = $compte->solde - $request->montant;

                DB::table('tblcomptebancaire')->where('id', $compte->id)->update(['solde' => $soldeApres, 'updated_at' => $now]);

                $compteMouvementId = DB::table('tblcomptebancaire_mouvements')->insertGetId([
                    'compte_id'   => $compte->id,
                    'type'        => 'depense',
                    'montant'     => $request->montant,
                    'solde_apres' => $soldeApres,
                    'motif'       => $request->libelle,
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            $chemin = null;
            if ($request->hasFile('piece')) {
                $chemin = $request->file('piece')->store('depenses/justificatifs', 'public');
            }

            $depenseId = DB::table('tbldepense')->insertGetId([
                'ecole_id'            => $ecoleId,
                'categorie'           => $request->categorie,
                'libelle'             => $request->libelle,
                'description'         => $request->description,
                'montant'             => $request->montant,
                'source_type'         => $request->source_type,
                'source_id'           => $request->source_id,
                'caisse_mouvement_id' => $caisseMouvementId,
                'compte_mouvement_id' => $compteMouvementId,
                'beneficiaire'        => $request->beneficiaire,
                'piece_justificative' => $chemin,
                'date_depense'        => $request->date_depense,
                'statut'              => 'validee',
                'user_id'             => Auth::id(),
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            $depense = DB::table('tbldepense')->where('id', $depenseId)->first();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dépense enregistrée avec succès', 'data' => $depense]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // Annuler une dépense (remboursement automatique de la source)
    public function annuler(Request $request, $slug, $id)
    {
        $request->validate(['motif' => 'nullable|string|max:255']);

        DB::beginTransaction();
        try {
            $depense = DB::table('tbldepense')->where('id', $id)->lockForUpdate()->first();

            if (!$depense) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Dépense introuvable.'], 404);
            }
            if ($depense->statut === 'annulee') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Cette dépense est déjà annulée.'], 422);
            }

            $now = Carbon::now();

            if ($depense->source_type === 'caisse') {
                $caisse = DB::table('tblcaisse')->where('id', $depense->source_id)->lockForUpdate()->first();
                $soldeApres = $caisse->solde + $depense->montant;

                DB::table('tblcaisse')->where('id', $caisse->id)->update(['solde' => $soldeApres, 'updated_at' => $now]);

                DB::table('tblcaisse_mouvements')->insert([
                    'caisse_id'   => $caisse->id,
                    'type'        => 'alimentation',
                    'montant'     => $depense->montant,
                    'solde_apres' => $soldeApres,
                    'motif'       => 'Annulation dépense : ' . $depense->libelle,
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            } else {
                $compte = DB::table('tblcomptebancaire')->where('id', $depense->source_id)->lockForUpdate()->first();
                $soldeApres = $compte->solde + $depense->montant;

                DB::table('tblcomptebancaire')->where('id', $compte->id)->update(['solde' => $soldeApres, 'updated_at' => $now]);

                DB::table('tblcomptebancaire_mouvements')->insert([
                    'compte_id'   => $compte->id,
                    'type'        => 'alimentation',
                    'montant'     => $depense->montant,
                    'solde_apres' => $soldeApres,
                    'motif'       => 'Annulation dépense : ' . $depense->libelle,
                    'user_id'     => Auth::id(),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            DB::table('tbldepense')->where('id', $id)->update([
                'statut'           => 'annulee',
                'motif_annulation' => $request->motif,
                'updated_at'       => $now,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dépense annulée, le montant a été remboursé à la source.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
