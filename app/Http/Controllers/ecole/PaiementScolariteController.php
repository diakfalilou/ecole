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

    /**
     * Historique des paiements, groupé par numéro de reçu.
     * Chaque reçu peut regrouper plusieurs lignes (ex: plusieurs mois payés en un seul paiement
     * mensuel) : on renvoie le montant total du reçu ainsi que le détail des lignes qui le composent.
     */
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

        $result = $paiements
            ->groupBy('v_numero_recu')
            ->map(function ($lignes) use ($moisNoms) {
                $premiere = $lignes->first();

                $moisListe = $lignes->pluck('i_mois')
                    ->filter()
                    ->map(fn ($m) => $moisNoms[$m] ?? $m)
                    ->values();

                return [
                    'numero_recu' => $premiere->v_numero_recu,
                    'date'        => \Carbon\Carbon::parse($premiere->d_date_paiement)->format('d/m/Y H:i'),
                    'date_tri'    => $premiere->d_date_paiement,
                    'type'        => $premiere->v_typeinscription,
                    'mode'        => $premiere->v_mode_paiement,
                    'mois'        => $moisListe->isNotEmpty() ? $moisListe->implode(', ') : '-',
                    'montant'     => $lignes->sum('f_montant'),
                    'details'     => $lignes->map(function ($l) use ($moisNoms) {
                        return [
                            'mois'    => $l->i_mois ? ($moisNoms[$l->i_mois] ?? $l->i_mois) : '-',
                            'mode'    => $l->v_mode_paiement,
                            'montant' => $l->f_montant,
                        ];
                    })->values(),
                ];
            })
            ->sortByDesc('date_tri')
            ->values()
            ->map(function ($ligne) {
                unset($ligne['date_tri']);
                return $ligne;
            });

        return response()->json($result);
    }

    /**
     * Affiche un reçu imprimable pour un numéro de reçu donné.
     * Regroupe toutes les lignes partageant ce numéro (ex : plusieurs mois payés d'un coup),
     * affiche le montant total du reçu, ET calcule le suivi global du mode de paiement
     * concerné (montant dû, déjà payé au total, reste à payer) ainsi que l'exonération
     * active éventuelle (rien n'est affiché si l'élève n'en a aucune).
     *
     * NB: pensez à ajouter la route correspondante, par ex:
     * Route::get('/{slug}/imprimer-recu/{numero_recu}', [PaiementScolariteController::class, 'imprimerRecu']);
     */
    public function imprimerRecu($slug, $numero_recu)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404);

        $lignes = DB::table('tblpaiement')
            ->where('v_numero_recu', $numero_recu)
            ->where('b_statut', 1)
            ->get();

        abort_if($lignes->isEmpty(), 404, 'Reçu introuvable.');

        $premiere = $lignes->first();

        $eleve  = DB::table('tbleleve')->where('i_eleve_id', $premiere->i_eleve_id)->first();
        $classe = DB::table('tblclasse')->where('i_classe_id', $premiere->i_classe_id)->first();
        $niveau = DB::table('tblniveau')->where('i_niveauID', $premiere->i_niveau_id)->first();

        $moisNoms = [
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre', 1 => 'Janvier',
            2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet',
        ];

        $modeLabels = [
            'inscription'   => 'Inscription',
            'reinscription' => 'Réinscription',
            'mensuelle'     => 'Mensualité',
            '1er_tranche'   => '1ère tranche',
            '2eme_tranche'  => '2ème tranche',
            '3eme_tranche'  => '3ème tranche',
            'annuelle'      => 'Annuelle',
        ];

        // Détail des lignes qui composent CE reçu précis
        $details = $lignes->map(function ($l) use ($moisNoms) {
            return [
                'mois'    => $l->i_mois ? ($moisNoms[$l->i_mois] ?? $l->i_mois) : '-',
                'mode'    => $l->v_mode_paiement,
                'montant' => $l->f_montant,
            ];
        });

        // v_typeinscription porte la vraie nature du paiement : 'inscription', 'reinscription' ou 'paiement'.
        // v_mode_paiement vaut toujours 'inscription' pour les 2 premiers cas (convention historique) ;
        // on reconstitue donc le "mode réel" à afficher/utiliser pour les calculs.
        $typeReel   = $premiere->v_typeinscription;
        $modeReel   = in_array($typeReel, ['inscription', 'reinscription']) ? $typeReel : $premiere->v_mode_paiement;
        $modeLabel  = $modeLabels[$modeReel] ?? $modeReel;

        $modalite = DB::table('tblmodaliteclasse')
            ->where('i_classeId', $premiere->i_classe_id)
            ->where('v_anneescolaire', $premiere->v_annee_scolaire)
            ->first();

        $exonerations = DB::table('tblexoneration')
            ->where('i_eleve_id', $premiere->i_eleve_id)
            ->where('v_annee_scolaire', $premiere->v_annee_scolaire)
            ->where('v_statut', 'active')
            ->get()
            ->keyBy('v_mode_paiement');

        $exoActuelle = $exonerations[$modeReel] ?? null;

        $eleveId  = $premiere->i_eleve_id;
        $annee    = $premiere->v_annee_scolaire;
        $suivi    = null; // Suivi du montant dû / payé / reste pour le mode concerné

        if ($modeReel === 'mensuelle') {
            $prixMensuel   = (float) ($modalite->d_prix_mensuelle ?? 0);
            $tousLesMois   = collect($moisNoms); // 10 mois de l'année scolaire

            $moisPayesNums = DB::table('tblpaiement')
                ->where('i_eleve_id', $eleveId)
                ->where('v_annee_scolaire', $annee)
                ->where('v_mode_paiement', 'mensuelle')
                ->where('b_statut', 1)
                ->pluck('i_mois')
                ->map(fn ($m) => (int) $m)
                ->unique();

            $montantExonere       = (float) ($exoActuelle->f_montant_exonere ?? 0);
            $montantOriginalTotal = $prixMensuel * $tousLesMois->count();
            $montantDu            = max(0, $montantOriginalTotal - $montantExonere);
            $montantPayeTotal     = (float) DB::table('tblpaiement')
                ->where('i_eleve_id', $eleveId)
                ->where('v_annee_scolaire', $annee)
                ->where('v_mode_paiement', 'mensuelle')
                ->where('b_statut', 1)
                ->sum('f_montant');
            $reste = max(0, $montantDu - $montantPayeTotal);

            $suivi = [
                'montant_original' => $montantOriginalTotal,
                'montant_du'       => $montantDu,
                'montant_paye'     => $montantPayeTotal,
                'reste'            => $reste,
                'mois_payes'       => $tousLesMois->only($moisPayesNums->toArray())->values(),
                'mois_non_payes'   => $tousLesMois->except($moisPayesNums->toArray())->values(),
            ];
        } elseif (in_array($modeReel, ['1er_tranche', '2eme_tranche', '3eme_tranche', 'annuelle'])) {
            $montantOriginal = (float) match ($modeReel) {
                '1er_tranche'  => $modalite->d_tranche1 ?? 0,
                '2eme_tranche' => $modalite->d_tranche2 ?? 0,
                '3eme_tranche' => $modalite->d_tranche3 ?? 0,
                'annuelle'     => $modalite->d_tranche_annuelle ?? 0,
                default        => 0,
            };

            $montantExonere   = (float) ($exoActuelle->f_montant_exonere ?? 0);
            $montantDu        = max(0, $montantOriginal - $montantExonere);
            $montantPayeTotal = (float) DB::table('tblpaiement')
                ->where('i_eleve_id', $eleveId)
                ->where('v_annee_scolaire', $annee)
                ->where('v_mode_paiement', $modeReel)
                ->where('b_statut', 1)
                ->sum('f_montant');
            $reste = max(0, $montantDu - $montantPayeTotal);

            $suivi = [
                'montant_original' => $montantOriginal,
                'montant_du'       => $montantDu,
                'montant_paye'     => $montantPayeTotal,
                'reste'            => $reste,
            ];
        } elseif (in_array($typeReel, ['inscription', 'reinscription'])) {
            $montantOriginal = (float) ($typeReel === 'inscription'
                ? ($modalite->d_pirx_inscription ?? 0)
                : ($modalite->d_prix_reinscription ?? 0));

            $montantExonere   = (float) ($exoActuelle->f_montant_exonere ?? 0);
            $montantDu        = max(0, $montantOriginal - $montantExonere);
            $montantPayeTotal = (float) DB::table('tblpaiement')
                ->where('i_eleve_id', $eleveId)
                ->where('v_annee_scolaire', $annee)
                ->where('v_typeinscription', $typeReel)
                ->where('b_statut', 1)
                ->sum('f_montant');
            $reste = max(0, $montantDu - $montantPayeTotal);

            $suivi = [
                'montant_original' => $montantOriginal,
                'montant_du'       => $montantDu,
                'montant_paye'     => $montantPayeTotal,
                'reste'            => $reste,
            ];
        }

        return view('ecoles.comptabilite.recu_impression', [
            'ecole'        => $ecole,
            'eleve'        => $eleve,
            'classe'       => $classe,
            'niveau'       => $niveau,
            'numeroRecu'   => $numero_recu,
            'date'         => \Carbon\Carbon::parse($premiere->d_date_paiement)->format('d/m/Y \à H:i'),
            'modeReel'     => $modeReel,
            'modeLabel'    => $modeLabel,
            'anneeSco'     => $annee,
            'details'      => $details,
            'montantTotal' => $lignes->sum('f_montant'),
            'exoneration'  => $exoActuelle, // null si l'élève ne bénéficie d'aucune exonération sur ce mode
            'suivi'        => $suivi,
            'modeLabels'   => $modeLabels,
        ]);
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

                $moisAPayer = array_values(array_filter($request->mois, fn($m) => !in_array((int)$m, $moisExistants)));
                $moisDejaPayes = array_values(array_diff($request->mois, $moisAPayer));

                if (empty($moisAPayer)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tous les mois sélectionnés ont déjà été payés.',
                    ], 422);
                }

                // Un seul et même numéro de reçu pour toutes les lignes de ce paiement
                $numeroRecu = 'RECU-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

                foreach ($moisAPayer as $mois) {
                    DB::table('tblpaiement')->insertGetId([
                        'v_numero_recu'     => $numeroRecu,
                        'i_eleve_id'        => $request->eleve_id,
                        'i_classe_id'       => $request->classe_id,
                        'i_niveau_id'       => $request->niveau_id,
                        'i_ecole_id'        => $ecole->i_idecole,
                        // La mensualité n'est ni une inscription ni une réinscription
                        'v_typeinscription' => 'paiement',
                        'v_mode_paiement'   => 'mensuelle',
                        'i_mois'            => $mois,
                        'f_montant'         => $montantParMois,
                        'v_annee_scolaire'  => $request->annee_scolaire,
                        'i_user_id'         => Auth::id(),
                        'd_date_paiement'   => now(),
                        'b_statut'          => 1,
                    ]);
                }

                return response()->json([
                    'success'     => true,
                    'message'     => count($moisAPayer) . ' paiement(s) enregistré(s) sous le reçu ' . $numeroRecu
                                  . (count($moisDejaPayes) > 0 ? ' (' . count($moisDejaPayes) . ' mois ignorés car déjà payés)' : ''),
                    'numero_recu' => $numeroRecu,
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
                // 'inscription' ou 'reinscription' si c'est bien le cas, sinon 'paiement'
                // (tranches, annuelle, etc. ne sont ni l'un ni l'autre)
                'v_typeinscription' => $isInscriptionOuReinscription ? $request->mode_paiement : 'paiement',
                'v_mode_paiement'   => $isInscriptionOuReinscription ? 'inscription' : (in_array($request->mode_paiement, $modesValides) ? $request->mode_paiement : 'mensuelle'),
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
