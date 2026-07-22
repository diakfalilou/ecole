<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class ResultatController extends Controller
{
    public function index($slug){
        abort_unless(PermissionHelper::hasRoute('resultat'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblanneesclaire')
            ->orderBy('i_idanneesclaire', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.resultat.resultat', compact('slug', 'annee_courante', 'niveaux', 'data_anneescolaire'));
    }

    /**
     * API JSON appelée par le Blade pour obtenir les calculs des résultats scolaires
     */
    public function getData(Request $request, $slug)
    {
        try {
        $ecole = $this->getEcole($slug);

        // 1. Récupération des filtres
        $annee = $request->get('annee_scolaire');
        $classeId = $request->get('classe_id');
        $niveauId = $request->get('niveau_id');
        $periodeType = $request->get('periode_type'); // 'trimestre', 'semestre' ou 'annuelle'
        $periodeNumero = $request->get('periode_numero'); // vide si annuelle

        // Infos pour l'en-tête
        $classe = DB::table('tblclasse')->where('i_classe_id', $classeId)->first();
        $niveau = DB::table('tblniveau')->where('i_niveauID', $niveauId)->first();

        // Barème selon le niveau : Maternelle/Primaire = /10, reste = /20
        $niveauNom = strtolower($niveau->v_niveaux ?? '');
        $isPrimaireOuMaternelle = str_contains($niveauNom, 'primaire') || str_contains($niveauNom, 'maternelle');
        $maxNote = $isPrimaireOuMaternelle ? 10 : 20;

        // Type de période réel de ce niveau (utile pour la boucle annuelle)
        $typePeriodeNiveau = $isPrimaireOuMaternelle ? 'trimestre' : 'semestre';
        $nombrePeriodes = $isPrimaireOuMaternelle ? 3 : 2;

        // 2. Liste de tous les élèves inscrits dans cette classe pour l'année sélectionnée
        $inscriptions = DB::table('tblinscription')
            ->join('tbleleve', 'tblinscription.i_eleve_id', '=', 'tbleleve.i_eleve_id')
            ->where('tblinscription.i_classe_id', $classeId)
            ->where('tblinscription.v_annee_scolaire', $annee)
            ->where('tblinscription.b_active', 1)
            ->select('tbleleve.i_eleve_id', 'tbleleve.v_nom', 'tbleleve.v_prenom')
            ->orderBy('tbleleve.v_nom')
            ->get();

        // 3. Récupération des notes de la classe
        if ($periodeType === 'annuelle') {
            // Toutes les notes de l'année pour cette classe, toutes périodes confondues
            $notesRaw = DB::table('tblnote')
                ->where('classe_id', $classeId)
                ->where('annee_scolaire', $annee)
                ->where('periode_type', $typePeriodeNiveau)
                ->get();
        } else {
            $notesRaw = DB::table('tblnote')
                ->where('classe_id', $classeId)
                ->where('annee_scolaire', $annee)
                ->where('periode_type', $periodeType)
                ->where('periode_numero', $periodeNumero)
                ->get();
        }

        // 4. Identifier les matières uniques évaluées pour cette classe sur cette période
        $matiereIds = $notesRaw->pluck('matiere_id')->unique();

        $matieres = DB::table('tblmatiere')
            ->where('ecole_id', $ecole->i_idecole)
            ->whereIn('id', $matiereIds)
            ->select('id', 'nom', 'code')
            ->get();

        // 5. Calculs des moyennes par élève
        $listeElevesCalculs = [];

        foreach ($inscriptions as $ins) {
            $notesDuGamin = $notesRaw->where('eleve_id', $ins->i_eleve_id);
            $notesEleve = [];
            $totalPointsEleve = 0;
            $nombreMatieresEvaluees = 0;

            foreach ($matieres as $mat) {
                if ($periodeType === 'annuelle') {
                    // Moyenne annuelle = moyenne des moyennes de chaque période disponible
                    $moyennesPeriodes = [];
                    for ($p = 1; $p <= $nombrePeriodes; $p++) {
                        $notesMatierePeriode = $notesDuGamin
                            ->where('matiere_id', $mat->id)
                            ->where('periode_numero', $p);

                        if ($notesMatierePeriode->isEmpty()) continue;

                        $moyennesPeriodes[] = $this->calculerMoyenneMatiere($notesMatierePeriode);
                    }

                    if (empty($moyennesPeriodes)) continue;

                    $moyenneMatiereArrondie = round(array_sum($moyennesPeriodes) / count($moyennesPeriodes), 2);
                } else {
                    $notesMatiere = $notesDuGamin->where('matiere_id', $mat->id);
                    if ($notesMatiere->isEmpty()) continue;

                    $moyenneMatiereArrondie = round($this->calculerMoyenneMatiere($notesMatiere), 2);
                }

                $notesEleve[$mat->id] = $moyenneMatiereArrondie;
                $totalPointsEleve += $moyenneMatiereArrondie;
                $nombreMatieresEvaluees++;
            }

            // Moyenne Générale Périodique (ou Annuelle) de l'élève
            $moyenneGenerale = $nombreMatieresEvaluees > 0 ? ($totalPointsEleve / $nombreMatieresEvaluees) : 0;

            $listeElevesCalculs[] = [
                'id' => $ins->i_eleve_id,
                'nom' => $ins->v_nom,
                'prenom' => $ins->v_prenom,
                'notes' => $notesEleve,
                'total_points' => round($totalPointsEleve, 2),
                'moyenne' => round($moyenneGenerale, 2),
                'rang' => 1 // Valeur par défaut avant le tri
            ];
        }

        // 6. Calcul automatique du RANG basé sur la moyenne générale descendante
        usort($listeElevesCalculs, function ($a, $b) {
            return $b['moyenne'] <=> $a['moyenne'];
        });

        // Attribution des rangs (prend en compte les ex æquo)
        $currentRang = 1;
        foreach ($listeElevesCalculs as $index => &$el) {
            if ($index > 0 && $el['moyenne'] < $listeElevesCalculs[$index - 1]['moyenne']) {
                $currentRang = $index + 1;
            }
            $el['rang'] = $currentRang;
        }
        unset($el); // Nettoyage de la référence

        // 7. Retourner l'ensemble au format attendu par ton JavaScript
        return response()->json([
            'ecole' => [
                'nom' => $ecole->v_nomecole,
                'logo' => $ecole->v_logoecole ?? null
            ],
            'annee_scolaire' => $annee,
            'classe' => $classe->v_nom_classe ?? 'Inconnue',
            'niveau' => $niveau->v_niveaux ?? 'Inconnu',
            'periode_type' => $periodeType,
            'periode_numero' => $periodeType === 'annuelle' ? null : $periodeNumero,
            'max_note' => $maxNote,
            'matieres' => $matieres,
            'eleves' => $listeElevesCalculs
        ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Calcule la moyenne d'une matière : (MoyenneCours + 2*NoteCompo) / 3
     * Si pas de compo saisie, renvoie la moyenne de cours brute.
     */
    private function calculerMoyenneMatiere($notesCollection)
    {
        $notesCours = $notesCollection->where('type', 'cours')->pluck('note');
        $noteCompo = $notesCollection->where('type', 'compo')->first();

        $moyenneCours = $notesCours->count() > 0 ? $notesCours->avg() : 0;

        if ($noteCompo) {
            return ($moyenneCours + (2 * $noteCompo->note)) / 3;
        }

        return $moyenneCours;
    }

    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }
}
