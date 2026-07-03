<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class RapportPaiementPeriodiqueController extends Controller
{
    public function rapport_periodique_paiement($slug, Request $request)
    {
        abort_unless(PermissionHelper::hasRoute('rapport.paiement.periodique'), 403);

        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404);

        $data_anneescolaire = DB::table('tblanneesclaire')->orderBy('i_idanneesclaire', 'desc')->get();
        $annee_courante     = $data_anneescolaire->first()->v_annesclaire ?? null;
        $annee              = $request->annee_scolaire ?? $annee_courante;
        $date_debut         = $request->date_debut;
        $date_fin           = $request->date_fin;
        $classe_id          = $request->classe_id;

        $classes = DB::table('tblclasse')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('v_nom_classe')
            ->get(['i_classe_id', 'v_nom_classe']);

        // Tous les élèves inscrits cette année dans cette école
        $inscriptions = DB::table('tblinscription as i')
            ->join('tbleleve as e', 'i.i_eleve_id', '=', 'e.i_eleve_id')
            ->join('tblclasse as c', 'i.i_classe_id', '=', 'c.i_classe_id')
            ->where('c.i_ecole_id', $ecole->i_idecole)
            ->where('i.v_annee_scolaire', $annee)
            ->where('i.b_active', 1)
            ->when($classe_id, fn($q) => $q->where('i.i_classe_id', $classe_id))
            ->select(
                'e.i_eleve_id', 'e.v_nom', 'e.v_prenom', 'e.v_matricule',
                'c.i_classe_id', 'c.v_nom_classe',
                'i.v_annee_scolaire'
            )
            ->get();

        $moisNoms = [
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre', 1 => 'Janvier',
            2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai',
            6 => 'Juin', 7 => 'Juillet'
        ];
        $tousLesMois = array_keys($moisNoms);

        // ================================================================
        // CONSTRUIRE LES DONNÉES PAR ÉLÈVE
        // ================================================================
        $donneesEleves = [];

        // Totaux globaux
        $totalInscriptionFacture    = 0;
        $totalInscriptionExo        = 0;
        $totalInscriptionDu         = 0;
        $totalInscriptionPaye       = 0;
        $totalInscriptionRestant    = 0;

        $totalTrancheFacture        = 0;
        $totalTrancheExo            = 0;
        $totalTrancheDu             = 0;
        $totalTranchePaye           = 0;
        $totalTrancheRestant        = 0;

        $totalEncaissement          = 0;
        $totalExonerationGlobale    = 0;

        foreach ($inscriptions as $ins) {
            $eleveId  = $ins->i_eleve_id;
            $classeId = $ins->i_classe_id;

            // Modalité de la classe
            $modalite = DB::table('tblmodaliteclasse')
                ->where('i_classeId', $classeId)
                ->where('v_anneescolaire', $annee)
                ->first();

            if (!$modalite) continue;

            // Exonérations de cet élève
            $exonerations = DB::table('tblexoneration')
                ->where('i_eleve_id', $eleveId)
                ->where('v_annee_scolaire', $annee)
                ->where('v_statut', 'active')
                ->get()
                ->keyBy('v_mode_paiement');

            // Paiements de cet élève
            $paiements = DB::table('tblpaiement')
                ->where('i_eleve_id', $eleveId)
                ->where('v_annee_scolaire', $annee)
                ->where('b_statut', 1)
                ->when($date_debut, fn($q) => $q->whereDate('d_date_paiement', '>=', $date_debut))
                ->when($date_fin,   fn($q) => $q->whereDate('d_date_paiement', '<=', $date_fin))
                ->get();

            // Détecter le mode de paiement utilisé par l'élève
            $aPayeMensuelle = $paiements->where('v_mode_paiement', 'mensuelle')->count() > 0;
            $aExoMensuelle  = isset($exonerations['mensuelle']);
            $aTranche1      = $paiements->where('v_mode_paiement', '1er_tranche')->count() > 0 || isset($exonerations['1er_tranche']);
            $aTranche2      = $paiements->where('v_mode_paiement', '2eme_tranche')->count() > 0 || isset($exonerations['2eme_tranche']);
            $aTranche3      = $paiements->where('v_mode_paiement', '3eme_tranche')->count() > 0 || isset($exonerations['3eme_tranche']);
            $aAnnuelle      = $paiements->where('v_mode_paiement', 'annuelle')->count() > 0     || isset($exonerations['annuelle']);

            // ---- BLOC INSCRIPTION / REINSCRIPTION ----
            $typeInscription = $paiements->whereIn('v_typeinscription', ['inscription', 'reinscription'])->first();
            $modeInsc        = $typeInscription?->v_typeinscription ?? 'inscription';

            $montantInscFact = (float) ($modeInsc === 'reinscription'
                ? $modalite->d_prix_reinscription
                : $modalite->d_pirx_inscription);

            $exoInsc         = $exonerations[$modeInsc] ?? null;
            $montantInscExo  = $exoInsc ? (float) $exoInsc->f_montant_exonere : 0;
            $montantInscDu   = max(0, $montantInscFact - $montantInscExo);
            $montantInscPaye = (float) $paiements
                ->whereIn('v_typeinscription', ['inscription', 'reinscription'])
                ->sum('f_montant');
            $montantInscRest = max(0, $montantInscDu - $montantInscPaye);

            // ---- BLOC TRANCHES / MENSUELLE ----
            $modeTranche      = null;
            $montantTrFact    = 0;
            $montantTrExo     = 0;
            $montantTrDu      = 0;
            $montantTrPaye    = 0;
            $montantTrRestant = 0;
            $detailMode       = '';

            if ($aPayeMensuelle || $aExoMensuelle) {
                $modeTranche   = 'mensuelle';
                $prixMois      = (float) $modalite->d_prix_mensuelle;
                $exoMens       = $exonerations['mensuelle'] ?? null;
                $moisExoneres  = $exoMens ? (int) round($exoMens->f_montant_exonere / ($prixMois ?: 1)) : 0;
                $moisPayes     = $paiements->where('v_mode_paiement', 'mensuelle')->pluck('i_mois')->unique()->count();
                $totalMois     = count($tousLesMois);

                $montantTrFact    = $prixMois * $totalMois;
                $montantTrExo     = $exoMens ? (float) $exoMens->f_montant_exonere : 0;
                $montantTrDu      = max(0, $montantTrFact - $montantTrExo);
                $montantTrPaye    = (float) $paiements->where('v_mode_paiement', 'mensuelle')->sum('f_montant');
                $montantTrRestant = max(0, $montantTrDu - $montantTrPaye);
                $detailMode       = $moisPayes . '/' . $totalMois . ' mois payés';

            } elseif ($aTranche1 || $aTranche2 || $aTranche3) {
                $modeTranche = 'tranches';
                foreach (['1er_tranche' => 'd_tranche1', '2eme_tranche' => 'd_tranche2', '3eme_tranche' => 'd_tranche3'] as $modeKey => $col) {
                    $fact    = (float) $modalite->$col;
                    $exoT    = $exonerations[$modeKey] ?? null;
                    $exoMnt  = $exoT ? (float) $exoT->f_montant_exonere : 0;
                    $du      = max(0, $fact - $exoMnt);
                    $paye    = (float) $paiements->where('v_mode_paiement', $modeKey)->sum('f_montant');

                    $montantTrFact    += $fact;
                    $montantTrExo     += $exoMnt;
                    $montantTrDu      += $du;
                    $montantTrPaye    += $paye;
                    $montantTrRestant += max(0, $du - $paye);
                }
                $detailMode = '1ère + 2ème + 3ème tranche';

            } elseif ($aAnnuelle) {
                $modeTranche      = 'annuelle';
                $exoAnn           = $exonerations['annuelle'] ?? null;
                $montantTrFact    = (float) $modalite->d_tranche_annuelle;
                $montantTrExo     = $exoAnn ? (float) $exoAnn->f_montant_exonere : 0;
                $montantTrDu      = max(0, $montantTrFact - $montantTrExo);
                $montantTrPaye    = (float) $paiements->where('v_mode_paiement', 'annuelle')->sum('f_montant');
                $montantTrRestant = max(0, $montantTrDu - $montantTrPaye);
                $detailMode       = 'Annuelle';
            }

            $totalPaye    = $montantInscPaye + $montantTrPaye;
            $totalExo     = $montantInscExo  + $montantTrExo;
            $totalRestant = $montantInscRest  + $montantTrRestant;

            $donneesEleves[] = [
                'eleve_id'        => $eleveId,
                'nom'             => $ins->v_nom . ' ' . $ins->v_prenom,
                'matricule'       => $ins->v_matricule ?? '-',
                'classe'          => $ins->v_nom_classe,
                // Inscription
                'insc_facture'    => $montantInscFact,
                'insc_exo'        => $montantInscExo,
                'insc_du'         => $montantInscDu,
                'insc_paye'       => $montantInscPaye,
                'insc_restant'    => $montantInscRest,
                // Tranche/mensuelle
                'mode_tranche'    => $modeTranche,
                'detail_mode'     => $detailMode,
                'tr_facture'      => $montantTrFact,
                'tr_exo'          => $montantTrExo,
                'tr_du'           => $montantTrDu,
                'tr_paye'         => $montantTrPaye,
                'tr_restant'      => $montantTrRestant,
                // Totaux
                'total_paye'      => $totalPaye,
                'total_exo'       => $totalExo,
                'total_restant'   => $totalRestant,
            ];

            // Cumuler les totaux globaux
            $totalInscriptionFacture  += $montantInscFact;
            $totalInscriptionExo      += $montantInscExo;
            $totalInscriptionDu       += $montantInscDu;
            $totalInscriptionPaye     += $montantInscPaye;
            $totalInscriptionRestant  += $montantInscRest;

            $totalTrancheFacture      += $montantTrFact;
            $totalTrancheExo          += $montantTrExo;
            $totalTrancheDu           += $montantTrDu;
            $totalTranchePaye         += $montantTrPaye;
            $totalTrancheRestant      += $montantTrRestant;

            $totalEncaissement        += $totalPaye;
            $totalExonerationGlobale  += $totalExo;
        }

        $totalRestantGlobal = $totalInscriptionRestant + $totalTrancheRestant;

        return view('ecoles.comptabilite.rapport_paiement_periodique', compact(
            'slug', 'data_anneescolaire', 'annee_courante', 'annee',
            'classes', 'classe_id', 'date_debut', 'date_fin',
            'donneesEleves',
            'totalInscriptionFacture', 'totalInscriptionExo', 'totalInscriptionDu',
            'totalInscriptionPaye',    'totalInscriptionRestant',
            'totalTrancheFacture',     'totalTrancheExo',     'totalTrancheDu',
            'totalTranchePaye',        'totalTrancheRestant',
            'totalEncaissement',       'totalExonerationGlobale', 'totalRestantGlobal'
        ));
    }
}
