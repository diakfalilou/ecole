<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class BulletinController extends Controller
{
    /**
     * Répartition des mois par type et numéro de période
     * (identique à celle utilisée côté saisie des notes)
     */
    private function moisParPeriode(string $type, int $numero): array
    {
        $map = [
            'trimestre' => [
                1 => ['Octobre', 'Novembre', 'Décembre'],
                2 => ['Janvier', 'Février', 'Mars'],
                3 => ['Avril', 'Mai', 'Juin'],
            ],
            'semestre' => [
                1 => ['Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février'],
                2 => ['Mars', 'Avril', 'Mai', 'Juin'],
            ],
        ];

        return $map[$type][$numero] ?? [];
    }

    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    /**
     * Page de filtre (année / niveau / classe / élève / période)
     */
    public function index($slug)
    {
        abort_unless(PermissionHelper::hasRoute('bulletin'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblanneesclaire')
            ->orderBy('i_idanneesclaire', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.bulletin.index', compact('slug', 'annee_courante', 'niveaux', 'data_anneescolaire'));
    }

    /**
     * Détermine l'appréciation selon une note et le barème max du niveau
     * (réutilisée pour l'appréciation par matière et l'appréciation générale)
     */
    private function appreciation(float $moyenne, float $maxNote): string
    {
        return match (true) {
            $moyenne >= $maxNote * 0.8 => 'Excellent',
            $moyenne >= $maxNote * 0.7 => 'Très bien',
            $moyenne >= $maxNote * 0.6 => 'Bien',
            $moyenne >= $maxNote * 0.5 => 'Assez bien',
            default => 'Insuffisant',
        };
    }

    /**
     * Calcule les lignes de matières + la moyenne générale pondérée pour un élève donné
     * (à partir d'un index de notes déjà indexé "matiereId_type_mois" => note)
     */
    private function calculerMoyennes($matieres, array $mois, array $index, float $maxNote): array
    {
        $lignes = [];
        $totalCoef = 0;
        $sumPonderee = 0;

        // Cumuls pour la ligne de totaux
        $totalMoyenneCours   = 0;
        $totalNoteCompo      = 0;
        $totalMoyenneMatiere = 0;

        foreach ($matieres as $m) {
            $sommeCours = 0;
            foreach ($mois as $mo) {
                $sommeCours += $index[$m->id . '_cours_' . $mo] ?? 0;
            }
            // Formule demandée : somme des notes mensuelles de la période / 3
            $moyenneCours = round($sommeCours / 3, 2);

            $noteCompo = $index[$m->id . '_compo_compo'] ?? 0;
            $moyenneMatiere = round(($moyenneCours + $noteCompo) / 2, 2);

            $coef = (float) ($m->coefficient ?? 1);
            $pondere = $moyenneMatiere * $coef; // toujours calculé pour la moyenne générale

            $totalCoef   += $coef;
            $sumPonderee += $pondere;

            $totalMoyenneCours   += $moyenneCours;
            $totalNoteCompo      += $noteCompo;
            $totalMoyenneMatiere += $moyenneMatiere;

            $lignes[] = [
                'matiere'         => $m->nom,
                'coefficient'     => $coef,
                'moyenne_cours'   => $moyenneCours,
                'note_compo'      => $noteCompo,
                'moyenne_matiere' => $moyenneMatiere,
                'appreciation'    => $this->appreciation($moyenneMatiere, $maxNote),
            ];
        }

        $moyenneGenerale = $totalCoef ? round($sumPonderee / $totalCoef, 2) : 0;

        $totaux = [
            'coefficient'     => $totalCoef,
            'moyenne_cours'   => round($totalMoyenneCours, 2),
            'note_compo'      => round($totalNoteCompo, 2),
            'moyenne_matiere' => round($totalMoyenneMatiere, 2),
        ];

        return [$lignes, $totalCoef, $moyenneGenerale, $totaux];
    }

    /**
     * Construit le bulletin d'un élève pour une période donnée
     * (moyenne cours = somme des notes mensuelles de la période / 3, moyenne matière = (moyenne cours + compo) / 2)
     */
    public function getBulletin(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'annee_scolaire' => 'required|string',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'eleve_id'       => 'required|integer',
            'periode_type'   => 'required|in:trimestre,semestre',
            'periode_numero' => 'required|integer',
        ]);

        $niveau = DB::table('tblniveau')
            ->where('i_niveauID', $request->niveau_id)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        abort_unless($niveau, 404, 'Niveau introuvable.');

        $classe = DB::table('tblclasse')
            ->where('i_classe_id', $request->classe_id)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        abort_unless($classe, 404, 'Classe introuvable.');

        $eleve = DB::table('tbleleve')
            ->where('i_eleve_id', $request->eleve_id)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->first();
        abort_unless($eleve, 404, 'Élève introuvable.');

        $nomNiveau = mb_strtolower($niveau->v_niveaux);
        $maxNote = (str_contains($nomNiveau, 'maternelle') || str_contains($nomNiveau, 'primaire')) ? 10 : 20;

        $mois = $this->moisParPeriode($request->periode_type, $request->periode_numero);

        $matieres = DB::table('tblclassematiere as cm')
            ->join('tblmatiere as m', 'm.id', '=', 'cm.matiere_id')
            ->where('cm.classe_id', $request->classe_id)
            ->where('m.ecole_id', $ecole->i_idecole)
            ->where('m.statut', 'active')
            ->select('m.id', 'm.nom', 'cm.coefficient')
            ->orderBy('m.nom')
            ->get();

        // ===== Notes de l'élève sélectionné =====
        $notesEleve = DB::table('tblnote')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('niveau_id', $request->niveau_id)
            ->where('classe_id', $request->classe_id)
            ->where('eleve_id', $request->eleve_id)
            ->where('periode_type', $request->periode_type)
            ->where('periode_numero', $request->periode_numero)
            ->get();

        $indexEleve = [];
        foreach ($notesEleve as $n) {
            $indexEleve[$n->matiere_id . '_' . $n->type . '_' . ($n->mois ?? 'compo')] = (float) $n->note;
        }

        [$lignes, $totalCoef, $moyenneGenerale, $totaux] = $this->calculerMoyennes($matieres, $mois, $indexEleve, $maxNote);

        $appreciation = $this->appreciation($moyenneGenerale, $maxNote);
        // $appreciation = match (true) {
        //     $moyenneGenerale >= $maxNote * 0.8 => 'Excellent',
        //     $moyenneGenerale >= $maxNote * 0.7 => 'Très bien',
        //     $moyenneGenerale >= $maxNote * 0.6 => 'Bien',
        //     $moyenneGenerale >= $maxNote * 0.5 => 'Assez bien',
        //     default => 'Insuffisant',
        // };

        // ===== Rang dans la classe =====
        // On récupère tous les élèves inscrits dans la classe pour cette année, puis on calcule
        // la moyenne générale de chacun sur la même période pour comparer.
        $classmates = DB::table('tblinscription as i')
            ->join('tbleleve as e', 'e.i_eleve_id', '=', 'i.i_eleve_id')
            ->where('i.i_classe_id', $request->classe_id)
            ->where('i.i_ecole_id', $ecole->i_idecole)
            ->where('i.v_annee_scolaire', $request->annee_scolaire)
            ->where('i.b_active', 1)
            ->where('e.b_desabled', 1)
            ->select('e.i_eleve_id as id')
            ->get();

        $allNotes = DB::table('tblnote')
            ->where('ecole_id', $ecole->i_idecole)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->where('niveau_id', $request->niveau_id)
            ->where('classe_id', $request->classe_id)
            ->where('periode_type', $request->periode_type)
            ->where('periode_numero', $request->periode_numero)
            ->get()
            ->groupBy('eleve_id');

        $moyennesClasse = [];
        foreach ($classmates as $cm) {
            $notesCm = $allNotes->get($cm->id, collect());
            $indexCm = [];
            foreach ($notesCm as $n) {
                $indexCm[$n->matiere_id . '_' . $n->type . '_' . ($n->mois ?? 'compo')] = (float) $n->note;
            }
            [, , $moyCm] = $this->calculerMoyennes($matieres, $mois, $indexCm, $maxNote);
            $moyennesClasse[$cm->id] = $moyCm;
        }

        $effectif = count($moyennesClasse);
        $rang = 1 + collect($moyennesClasse)->filter(fn ($m) => $m > $moyenneGenerale)->count();
        $rangLabel = $rang === 1 ? '1er' : $rang . 'e';

        // ===== Statistiques de la classe pour cette période =====
        $valeursClasse = array_values($moyennesClasse);
        $statMoyenneClasse = count($valeursClasse) ? round(array_sum($valeursClasse) / count($valeursClasse), 2) : 0;
        $statMoyenneMax = count($valeursClasse) ? round(max($valeursClasse), 2) : 0;
        $statMoyenneMin = count($valeursClasse) ? round(min($valeursClasse), 2) : 0;

        return response()->json([
            'ecole'             => [
                'nom'  => $ecole->v_slugecole,
                'logo' => $ecole->logo ? asset($ecole->logo) : null,
            ],
            'eleve'             => ['nom' => $eleve->v_nom, 'prenom' => $eleve->v_prenom, 'matricule' => $eleve->v_matricule ?? null],
            'classe'            => $classe->v_nom_classe,
            'niveau'            => $niveau->v_niveaux,
            'annee_scolaire'    => $request->annee_scolaire,
            'periode_type'      => $request->periode_type,
            'periode_numero'    => $request->periode_numero,
            'max_note'          => $maxNote,
            'lignes'            => $lignes,
            'totaux' => $totaux,
            'moyenne_generale'  => $moyenneGenerale,
            'appreciation'      => $appreciation,
            'rang'              => $rang,
            'rang_label'        => $rangLabel,
            'effectif'          => $effectif,
            'stats'             => [
                'moyenne_classe' => $statMoyenneClasse,
                'moyenne_max'    => $statMoyenneMax,
                'moyenne_min'    => $statMoyenneMin,
            ],
        ]);
    }
}
