<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class SituationGlePaiementController extends Controller
{
    public function situation_gen_paiement($slug, Request $request)
    {
        abort_unless(PermissionHelper::hasRoute('situation.general.classe'), 403);

        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404);

        $data_anneescolaire = DB::table('tblanneesclaire')->orderBy('i_idanneesclaire', 'desc')->get();
        $annee_courante     = $data_anneescolaire->first()->v_annesclaire ?? null;
        $annee              = $request->annee_scolaire ?? $annee_courante;

        $niveau_id = $request->niveau_id;
        $classe_id = $request->classe_id;

        // ------------------------------------------------------------
        // Niveaux utilisés par cette école (déduits via les classes)
        // HYPOTHÈSE : tblniveau(i_niveau_id, v_niveaux) + tblclasse.i_niveau_id
        // ------------------------------------------------------------
        $niveaux = DB::table('tblclasse as c')
            ->join('tblniveau as n', 'c.i_niveau_id', '=', 'n.i_niveauID')
            ->where('c.i_ecole_id', $ecole->i_idecole)
            ->select('n.i_niveauID', 'n.v_niveaux')
            ->distinct()
            ->orderBy('n.v_niveaux')
            ->get();

        // Toutes les classes de l'école (pour le filtre déroulant)
        $toutesLesClasses = DB::table('tblclasse')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('v_nom_classe')
            ->get(['i_classe_id', 'v_nom_classe']);

        // Classes filtrées (par niveau et/ou classe si demandé)
        $classesQuery = DB::table('tblclasse as c')
            ->leftJoin('tblniveau as n', 'c.i_niveau_id', '=', 'n.i_niveauID')
            ->where('c.i_ecole_id', $ecole->i_idecole)
            ->when($niveau_id, fn($q) => $q->where('c.i_niveau_id', $niveau_id))
            ->when($classe_id, fn($q) => $q->where('c.i_classe_id', $classe_id))
            ->select('c.i_classe_id', 'c.v_nom_classe', 'c.i_niveau_id', 'n.v_niveaux')
            ->orderBy('n.v_niveaux')
            ->orderBy('c.v_nom_classe')
            ->get();

        $situationClasses    = [];
        $totalAttenduGlobal  = 0;
        $totalPayeGlobal     = 0;
        $totalEffectifGlobal = 0;

        foreach ($classesQuery as $classe) {

            // Modalités tarifaires de cette classe pour l'année choisie
            $modalite = DB::table('tblmodaliteclasse')
                ->where('i_classeId', $classe->i_classe_id)
                ->where('v_anneescolaire', $annee)
                ->first();

            // Élèves inscrits (actifs) dans cette classe pour l'année choisie
            $inscriptions = DB::table('tblinscription as i')
                ->join('tbleleve as e', 'i.i_eleve_id', '=', 'e.i_eleve_id')
                ->where('i.i_classe_id', $classe->i_classe_id)
                ->where('i.v_annee_scolaire', $annee)
                ->where('i.b_active', 1)
                ->select('e.i_eleve_id')
                ->get();

            $effectif = $inscriptions->count();

            $classeAttendu = 0;
            $classePaye    = 0;
            $eleveAJour    = 0;
            $eleveDebiteur = 0;

            if ($modalite) {
                foreach ($inscriptions as $ins) {
                    $eleveId = $ins->i_eleve_id;

                    $paiements = DB::table('tblpaiement')
                        ->where('i_eleve_id', $eleveId)
                        ->where('v_annee_scolaire', $annee)
                        ->where('b_statut', 1)
                        ->get();

                    $exonerations = DB::table('tblexoneration')
                        ->where('i_eleve_id', $eleveId)
                        ->where('v_annee_scolaire', $annee)
                        ->where('v_statut', 'active')
                        ->get();

                    // Type d'inscription réellement facturé (par défaut "inscription")
                    $typeInscription = $paiements
                        ->whereIn('v_typeinscription', ['inscription', 'reinscription'])
                        ->first()?->v_typeinscription ?? 'inscription';

                    $prixInscription = (float) ($typeInscription === 'reinscription'
                        ? $modalite->d_prix_reinscription
                        : $modalite->d_pirx_inscription);

                    // Montant scolarité attendu = montant annuel total de référence
                    // (identique quel que soit le mode de paiement choisi par l'élève)
                    $prixScolarite = (float) $modalite->d_tranche_annuelle;

                    $montantExonere = (float) $exonerations->sum('f_montant_exonere');
                    $montantAttendu = max(0, ($prixInscription + $prixScolarite) - $montantExonere);

                    $montantPaye = (float) $paiements->sum('f_montant');

                    $classeAttendu += $montantAttendu;
                    $classePaye    += $montantPaye;

                    if ($montantPaye >= $montantAttendu) {
                        $eleveAJour++;
                    } else {
                        $eleveDebiteur++;
                    }
                }
            }

            $reste = max(0, $classeAttendu - $classePaye);
            $taux  = $classeAttendu > 0 ? round(($classePaye / $classeAttendu) * 100, 1) : 0;

            if ($taux >= 80) {
                $statutLabel = 'Bon';    $statutClass = 'success';
            } elseif ($taux >= 50) {
                $statutLabel = 'Moyen';  $statutClass = 'warning';
            } else {
                $statutLabel = 'Faible'; $statutClass = 'danger';
            }

            $situationClasses[] = [
                'niveau'           => $classe->v_niveaux ?? 'N/A',
                'classe'           => $classe->v_nom_classe,
                'effectif'         => $effectif,
                'total_attendu'    => $classeAttendu,
                'total_paye'       => $classePaye,
                'reste'            => $reste,
                'taux'             => $taux,
                'eleves_a_jour'    => $eleveAJour,
                'eleves_debiteurs' => $eleveDebiteur,
                'statut_label'     => $statutLabel,
                'statut_class'     => $statutClass,
            ];

            $totalAttenduGlobal  += $classeAttendu;
            $totalPayeGlobal     += $classePaye;
            $totalEffectifGlobal += $effectif;
        }

        $totalResteGlobal = max(0, $totalAttenduGlobal - $totalPayeGlobal);
        $tauxGlobal        = $totalAttenduGlobal > 0
            ? round(($totalPayeGlobal / $totalAttenduGlobal) * 100, 1)
            : 0;

        // Classement des classes par taux de recouvrement
        $classesTriees = collect($situationClasses)->sortByDesc('taux')->values();

        $classesPerformantes = $classesTriees
            ->filter(fn($c) => $c['effectif'] > 0)
            ->take(4)
            ->values();

        $classesASuivre = $classesTriees
            ->filter(fn($c) => $c['effectif'] > 0)
            ->sortBy('taux')
            ->take(4)
            ->values();

        return view('ecoles.comptabilite.situation_general_classe', compact(
            'slug', 'ecole',
            'data_anneescolaire', 'annee_courante', 'annee',
            'niveaux', 'niveau_id',
            'toutesLesClasses', 'classe_id',
            'situationClasses',
            'totalAttenduGlobal', 'totalPayeGlobal', 'totalResteGlobal', 'tauxGlobal',
            'totalEffectifGlobal',
            'classesPerformantes', 'classesASuivre'
        ));
    }
}
