<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaiementScolariteController extends Controller
{
    //

    public function paiement_scolarite($slug){
        abort_unless(PermissionHelper::hasRoute('paiement.scolarite'),403);
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        $data_anneescolaire = DB::table('tblanneesclaire')->orderBy('i_idanneesclaire','desc')->get();
        // Année scolaire en cours (la plus récente)
        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;
        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
        ->get();
        return view('ecoles.comptabilite.paiement_scolarite',compact('slug','data_anneescolaire','annee_courante','niveaux'));
    }

    public function getElevesByClasse($classeId)
    {
        $annee = request('annee_scolaire');

        $eleves = DB::table('tblinscription')
            ->join('tbleleve', 'tblinscription.i_eleve_id', '=', 'tbleleve.i_eleve_id')
            ->where('tblinscription.i_classe_id', $classeId)
            ->where('tblinscription.b_active', 1)
            ->when($annee, function ($query) use ($annee) {
                return $query->where('tblinscription.v_annee_scolaire', $annee);
            })
            ->select('tbleleve.i_eleve_id', 'tbleleve.v_nom', 'tbleleve.v_prenom', 'tbleleve.v_matricule')
            ->get();

        return response()->json($eleves);
    }

    public function getEleveInfo($eleveId)
    {
        $eleve = DB::table('tbleleve')
            ->join('tblinscription', 'tbleleve.i_eleve_id', '=', 'tblinscription.i_eleve_id')
            ->join('tblclasse', 'tblinscription.i_classe_id', '=', 'tblclasse.i_classe_id')
            ->join('tblniveau', 'tblinscription.i_niveau_id', '=', 'tblniveau.i_niveauID')
            ->where('tbleleve.i_eleve_id', $eleveId)
            ->select(
                'tbleleve.v_nom',
                'tbleleve.v_prenom',
                'tbleleve.v_matricule',
                'tbleleve.v_genre',
                'tbleleve.d_date_naissance',
                'tbleleve.v_telephone',
                'tbleleve.v_email',
                'tbleleve.v_adresse',
                'tbleleve.v_photo',
                'tblclasse.v_nom_classe',
                'tblniveau.v_niveaux'
            )
            ->first();

        return response()->json($eleve);
    }

    public function getMontantTranche(Request $request)
    {
        $modalite = DB::table('tblmodaliteclasse')
            ->where('i_classeId', $request->classe_id)
            ->where('v_anneescolaire', $request->annee_scolaire)
            ->first();

        if (!$modalite) {
            return response()->json(['montant' => 0, 'montant_original' => 0, 'montant_exo' => 0]);
        }

        $modePaiement = $request->mode_paiement;

        $montantOriginal = match($modePaiement) {
            'inscription'   => (float) $modalite->d_pirx_inscription,
            'reinscription' => (float) $modalite->d_prix_reinscription,
            'mensuelle'     => (float) $modalite->d_prix_mensuelle,
            '1er_tranche'   => (float) $modalite->d_tranche1,
            '2eme_tranche'  => (float) $modalite->d_tranche2,
            '3eme_tranche'  => (float) $modalite->d_tranche3,
            'annuelle'      => (float) $modalite->d_tranche_annuelle,
            default         => 0
        };

        // Vérifier si une exonération active existe pour ce mode
        $exo = null;
        if ($request->eleve_id) {
            $exo = DB::table('tblexoneration')
                ->where('i_eleve_id', $request->eleve_id)
                ->where('v_annee_scolaire', $request->annee_scolaire)
                ->where('v_mode_paiement', $modePaiement)
                ->where('v_statut', 'active')
                ->first();
        }

        $montantExo  = $exo ? (float) $exo->f_montant_exonere : 0;
        $montantFinal = max(0, $montantOriginal - $montantExo);

        return response()->json([
            'montant'           => $montantFinal,
            'montant_original'  => $montantOriginal,
            'montant_exo'       => $montantExo,
            'type_exo'          => $exo?->v_type_exoneration,
            'autorise_par'      => $exo?->v_autorise_par,
        ]);
    }


    public function getStatutPaiement($slug, $eleveId, Request $request)
    {
        $classeId      = $request->classe_id;
        $anneeScolaire = $request->annee_scolaire;

        // Inscription / Réinscription déjà payées ?
        $inscriptionPayee = DB::table('tblpaiement')
            ->where('i_eleve_id', $eleveId)
            ->where('v_annee_scolaire', $anneeScolaire)
            ->where('v_typeinscription', 'inscription')
            ->where('b_statut', 1)
            ->exists();

        $reinscriptionPayee = DB::table('tblpaiement')
            ->where('i_eleve_id', $eleveId)
            ->where('v_annee_scolaire', $anneeScolaire)
            ->where('v_typeinscription', 'reinscription')
            ->where('b_statut', 1)
            ->exists();

        // Mois déjà payés (pour mensuelle)
        $moisPayes = DB::table('tblpaiement')
            ->where('i_eleve_id', $eleveId)
            ->where('v_annee_scolaire', $anneeScolaire)
            ->where('v_mode_paiement', 'mensuelle')
            ->where('b_statut', 1)
            ->pluck('i_mois');

        // Montant déjà payé par tranche
        $totalParTranche = DB::table('tblpaiement')
            ->select('v_mode_paiement', DB::raw('SUM(f_montant) as total_paye'))
            ->where('i_eleve_id', $eleveId)
            ->where('v_annee_scolaire', $anneeScolaire)
            ->whereIn('v_mode_paiement', ['1er_tranche', '2eme_tranche', '3eme_tranche', 'annuelle'])
            ->where('b_statut', 1)
            ->groupBy('v_mode_paiement')
            ->pluck('total_paye', 'v_mode_paiement');

        // ===================== EXONERATIONS =====================
        $exonerations = DB::table('tblexoneration')
            ->where('i_eleve_id', $eleveId)
            ->where('v_annee_scolaire', $anneeScolaire)
            ->where('v_statut', 'active')
            ->get(['v_mode_paiement', 'v_type_exoneration', 'f_pourcentage',
                    'f_montant_exonere', 'f_montant_initial', 'v_autorise_par']);

        $exoParMode = [];
        foreach ($exonerations as $exo) {
            $exoParMode[$exo->v_mode_paiement] = [
                'type'         => $exo->v_type_exoneration,
                'pourcentage'  => (float) $exo->f_pourcentage,
                'montant_exo'  => (float) $exo->f_montant_exonere,
                'montant_init' => (float) $exo->f_montant_initial,
                'autorise_par' => $exo->v_autorise_par,
            ];
        }

        return response()->json([
            'inscription_payee'   => $inscriptionPayee,
            'reinscription_payee' => $reinscriptionPayee,
            'mois_payes'          => $moisPayes,
            'total_par_tranche'   => $totalParTranche,
            'exonerations'        => $exoParMode,
        ]);
    }

    public function getHistoriquePaiement($slug, $eleveId)
    {
        $moisNoms = [
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre', 1 => 'Janvier',
            2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet'
        ];

        $paiements = DB::table('tblpaiement')
            ->where('i_eleve_id', $eleveId)
            ->where('b_statut', 1)
            ->orderBy('d_date_paiement', 'desc')
            ->get();

        $result = $paiements->map(function ($p) use ($moisNoms) {
            return [
                'date'        => \Carbon\Carbon::parse($p->d_date_paiement)->format('d/m/Y H:i'),
                'type'        => $p->v_typeinscription,
                'mode'        => $p->v_mode_paiement,
                'mois'        => $p->i_mois ? ($moisNoms[$p->i_mois] ?? '') : '-',
                'montant'     => $p->f_montant,
                'numero_recu' => $p->v_numero_recu,
            ];
        });

        return response()->json($result);
    }
public function enregistrerPaiement(Request $request)
{
    $request->validate([
        'eleve_id'          => 'required|integer',
        'classe_id'         => 'required|integer',
        'niveau_id'         => 'required|integer',
        'type_inscription'  => 'nullable|in:inscription,reinscription',
        'mode_paiement'     => 'required|string',
        'montant'           => 'required|numeric',
        'annee_scolaire'    => 'required|string',
        'mois'              => 'nullable|array',
        'slug'              => 'required|string',
    ]);

    $ecole = DB::table('tbecole')->where('v_slugecole', $request->slug)->first();
    if (!$ecole) {
        return response()->json(['success' => false, 'message' => 'École introuvable.'], 404);
    }

    $modesValides = ['mensuelle', '1er_tranche', '2eme_tranche', '3eme_tranche', 'annuelle'];

    try {
        // ===================== CAS MENSUELLE =====================
        if ($request->mode_paiement === 'mensuelle' && !empty($request->mois)) {
            $montantParMois = $request->montant_par_mois ?? 0;
            $lignesInserees = [];
            $moisDejaPayes  = [];

            // Vérifier les mois déjà payés
            $moisExistants = DB::table('tblpaiement')
                ->where('i_eleve_id', $request->eleve_id)
                ->where('v_annee_scolaire', $request->annee_scolaire)
                ->where('v_mode_paiement', 'mensuelle')
                ->where('b_statut', 1)
                ->pluck('i_mois')
                ->map(fn($m) => (int)$m)
                ->toArray();

            foreach ($request->mois as $mois) {
                // Ignorer les mois déjà payés
                if (in_array((int)$mois, $moisExistants)) {
                    $moisDejaPayes[] = $mois;
                    continue;
                }

                $numeroRecu = 'RECU-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

                DB::table('tblpaiement')->insertGetId([
                    'v_numero_recu'     => $numeroRecu,
                    'i_eleve_id'        => $request->eleve_id,
                    'i_classe_id'       => $request->classe_id,
                    'i_niveau_id'       => $request->niveau_id,
                    'i_ecole_id'        => $ecole->i_idecole,
                    'v_typeinscription' => $request->type_inscription ?? 'inscription',
                    'v_mode_paiement'   => 'mensuelle',
                    'i_mois'            => $mois,
                    'f_montant'         => $montantParMois,
                    'v_annee_scolaire'  => $request->annee_scolaire,
                    'i_user_id'         => Auth::id(),
                    'd_date_paiement'   => now(),
                    'b_statut'          => 1,
                ]);

                $lignesInserees[] = $numeroRecu;
            }

            if (empty($lignesInserees)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tous les mois sélectionnés ont déjà été payés.',
                ], 422);
            }

            return response()->json([
                'success'     => true,
                'message'     => count($lignesInserees) . ' paiement(s) enregistré(s).'
                              . (count($moisDejaPayes) > 0 ? ' (' . count($moisDejaPayes) . ' mois ignorés car déjà payés)' : ''),
                'numero_recu' => implode(', ', $lignesInserees),
            ]);
        }

        // ===================== CAS INSCRIPTION / REINSCRIPTION =====================
        $isInscriptionOuReinscription = in_array($request->mode_paiement, ['inscription', 'reinscription']);

        if ($isInscriptionOuReinscription) {
            // Vérifier si déjà payé
            $dejaPayee = DB::table('tblpaiement')
                ->where('i_eleve_id', $request->eleve_id)
                ->where('v_annee_scolaire', $request->annee_scolaire)
                ->where('v_typeinscription', $request->mode_paiement)
                ->where('b_statut', 1)
                ->exists();

            if ($dejaPayee) {
                $label = $request->mode_paiement === 'inscription' ? 'L\'inscription' : 'La réinscription';
                return response()->json([
                    'success' => false,
                    'message' => $label . ' a déjà été payée pour cette année scolaire.',
                ], 422);
            }
        }

        // ===================== CAS TRANCHES : vérifier le reste à payer =====================
        if (in_array($request->mode_paiement, ['1er_tranche', '2eme_tranche', '3eme_tranche', 'annuelle'])) {
            $modalite = DB::table('tblmodaliteclasse')
                ->where('i_classeId', $request->classe_id)
                ->where('v_anneescolaire', $request->annee_scolaire)
                ->first();

            if ($modalite) {
                $montantTotal = match($request->mode_paiement) {
                    '1er_tranche'  => $modalite->d_tranche1,
                    '2eme_tranche' => $modalite->d_tranche2,
                    '3eme_tranche' => $modalite->d_tranche3,
                    'annuelle'     => $modalite->d_tranche_annuelle,
                    default        => 0
                };

                $dejaPaye = DB::table('tblpaiement')
                    ->where('i_eleve_id', $request->eleve_id)
                    ->where('v_annee_scolaire', $request->annee_scolaire)
                    ->where('v_mode_paiement', $request->mode_paiement)
                    ->where('b_statut', 1)
                    ->sum('f_montant');

                $reste = $montantTotal - $dejaPaye;

                if ($reste <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette tranche a déjà été entièrement payée.',
                    ], 422);
                }

                if ($request->montant > $reste) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le montant dépasse le reste à payer (' . number_format($reste, 0, ',', ' ') . ').',
                    ], 422);
                }
            }
        }

        // ===================== INSERTION =====================
        $numeroRecu = 'RECU-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        DB::table('tblpaiement')->insertGetId([
            'v_numero_recu'     => $numeroRecu,
            'i_eleve_id'        => $request->eleve_id,
            'i_classe_id'       => $request->classe_id,
            'i_niveau_id'       => $request->niveau_id,
            'i_ecole_id'        => $ecole->i_idecole,
            'v_typeinscription' => $request->type_inscription
                                ?? ($isInscriptionOuReinscription ? $request->mode_paiement : 'inscription'),
            'v_mode_paiement'   => $isInscriptionOuReinscription
                                ? 'mensuelle'
                                : (in_array($request->mode_paiement, $modesValides) ? $request->mode_paiement : 'mensuelle'),
            'i_mois'            => null,
            'f_montant'         => $request->montant,
            'v_annee_scolaire'  => $request->annee_scolaire,
            'i_user_id'         => Auth::id(),
            'd_date_paiement'   => now(),
            'b_statut'          => 1,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Paiement enregistré avec succès.',
            'numero_recu' => $numeroRecu,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()
        ], 500);
    }
}



}
