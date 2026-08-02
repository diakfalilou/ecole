<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ChargerNoteController extends Controller
{
    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    public function charger_note($slug)
    {
        abort_unless(PermissionHelper::hasRoute('charger.note'), 403);
        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblcontrat')
            ->orderBy('i_contrat_id', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annee_scolaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.notes.charger_note', compact('data_anneescolaire', 'niveaux', 'annee_courante', 'slug'));
    }

    // AJAX : classes actives liées à un niveau, pour l'école courante
    public function classes_par_niveau($slug, $niveauId)
    {
        $ecole = $this->getEcole($slug);

        $classes = DB::table('tblclasse')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('i_niveau_id', $niveauId)
            ->where('b_desabled', 1)
            ->orderBy('v_nom_classe', 'asc')
            ->get(['i_classe_id', 'v_nom_classe']);

        return response()->json($classes);
    }

    // AJAX : matières rattachées à une classe (via tblclassematiere)
    public function matieres_par_classe($slug, $classeId)
    {
        $ecole = $this->getEcole($slug);

        $matieres = DB::table('tblclassematiere')
            ->join('tblmatiere', 'tblmatiere.id', '=', 'tblclassematiere.matiere_id')
            ->where('tblclassematiere.classe_id', $classeId)
            ->where('tblclassematiere.ecole_id', $ecole->i_idecole)
            ->where('tblmatiere.statut', 'active')
            ->select('tblmatiere.id', 'tblmatiere.nom')
            ->orderBy('tblmatiere.nom')
            ->get();

        return response()->json($matieres);
    }

    // AJAX : upload + extraction du fichier de notes (excel/csv/pdf/image)
    public function importFichier(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'fichier'        => 'required|file|mimes:xlsx,xls,csv,pdf,jpg,jpeg,png|max:10240',
            'annee_scolaire' => 'required|string',
            'niveau_id'      => 'required|integer',
            'classe_id'      => 'required|integer',
            'matiere_id'     => 'required|integer',
            'periode_type'   => 'required|in:trimestre,semestre',
            'periode_numero' => 'required|integer',
            'type_note'      => 'required|in:cours,compo',
            'mois'           => 'nullable|string',
        ]);

        if ($request->type_note === 'cours' && !$request->filled('mois')) {
            return response()->json(['message' => 'Le mois est requis pour une note de cours.'], 422);
        }

        $colonneCible = $request->type_note === 'compo' ? 'COMPO' : strtoupper($request->mois);

        $file = $request->file('fichier');
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        try {
            if (in_array($ext, ['xlsx', 'xls', 'csv'])) {
                $extraits = $this->extraireNotesDepuisTableur($path, $colonneCible);
            } else {
                $mime = match ($ext) {
                    'pdf'  => 'application/pdf',
                    'png'  => 'image/png',
                    default => 'image/jpeg',
                };
                $extraits = $this->extraireNotesParIA($path, $mime, $colonneCible);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Erreur lors de la lecture du fichier : ' . $e->getMessage()], 422);
        }

        // Élèves inscrits actifs de la classe pour l'année sélectionnée
        $eleves = DB::table('tblinscription')
            ->join('tbleleve', 'tbleleve.i_eleve_id', '=', 'tblinscription.i_eleve_id')
            ->where('tblinscription.i_classe_id', $request->classe_id)
            ->where('tblinscription.v_annee_scolaire', $request->annee_scolaire)
            ->where('tblinscription.b_active', 1)
            ->select('tbleleve.i_eleve_id', 'tbleleve.v_nom', 'tbleleve.v_prenom')
            ->orderBy('tbleleve.v_nom')
            ->get();

        $lignes = $this->matcherEleves($extraits, $eleves);

        $batchId = (string) Str::uuid();
        $now = now();

        $inserts = [];
        foreach ($lignes as $ligne) {
            $inserts[] = [
                'batch_id'        => $batchId,
                'ecole_id'        => $ecole->i_idecole,
                'annee_scolaire'  => $request->annee_scolaire,
                'niveau_id'       => $request->niveau_id,
                'classe_id'       => $request->classe_id,
                'matiere_id'      => $request->matiere_id,
                'periode_type'    => $request->periode_type,
                'periode_numero'  => $request->periode_numero,
                'type'            => $request->type_note,
                'mois'            => $request->type_note === 'cours' ? $request->mois : null,
                'eleve_id'        => $ligne['eleve_id'],
                'nom_extrait'     => $ligne['nom_extrait'],
                'note'            => $ligne['note'],
                'statut_match'    => $ligne['statut_match'],
                'fichier_origine' => $file->getClientOriginalName(),
                'created_by'      => Auth::id(),
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if (!empty($inserts)) {
            DB::table('tblnote_temp')->insert($inserts);
        }

        return response()->json([
            'batch_id' => $batchId,
            'colonne'  => $colonneCible,
            'eleves'   => $lignes,
        ]);
    }

    // AJAX : enregistrement définitif des notes (depuis le tableau éditable)
    public function enregistrerNotes(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $data = $request->validate([
            'batch_id'         => 'required|string',
            'annee_scolaire'   => 'required|string',
            'niveau_id'        => 'required|integer',
            'classe_id'        => 'required|integer',
            'matiere_id'       => 'required|integer',
            'periode_type'     => 'required|in:trimestre,semestre',
            'periode_numero'   => 'required|integer',
            'type_note'        => 'required|in:cours,compo',
            'mois'             => 'nullable|string',
            'lignes'           => 'required|array|min:1',
            'lignes.*.eleve_id'=> 'required|integer',
            'lignes.*.note'    => 'nullable|numeric|min:0|max:20',
        ]);

        $now = now();
        $count = 0;

        foreach ($data['lignes'] as $ligne) {
            if ($ligne['note'] === null || $ligne['note'] === '') {
                continue;
            }

            DB::table('tblnote')->updateOrInsert(
                [
                    'ecole_id'       => $ecole->i_idecole,
                    'annee_scolaire' => $data['annee_scolaire'],
                    'eleve_id'       => $ligne['eleve_id'],
                    'matiere_id'     => $data['matiere_id'],
                    'periode_type'   => $data['periode_type'],
                    'periode_numero' => $data['periode_numero'],
                    'type'           => $data['type_note'],
                    'mois'           => $data['type_note'] === 'cours' ? $data['mois'] : null,
                ],
                [
                    'niveau_id'  => $data['niveau_id'],
                    'classe_id'  => $data['classe_id'],
                    'note'       => $ligne['note'],
                    'created_by' => Auth::id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $count++;
        }

        // Nettoyage de la table auxiliaire pour ce batch
        DB::table('tblnote_temp')->where('batch_id', $data['batch_id'])->delete();

        return response()->json(['success' => true, 'nb_notes_enregistrees' => $count]);
    }

    /**
     * Lecture directe d'un fichier Excel/CSV : cherche l'en-tête correspondant
     * à la colonne cible (mois ou COMPO) et retourne [{nom, note}, ...]
     */
    private function extraireNotesDepuisTableur(string $filePath, string $colonneCible): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return [];
        }

        $header = array_map(fn ($h) => Str::upper(trim((string) $h)), $rows[0]);

        $colIndex = null;
        foreach ($header as $i => $h) {
            if ($h === $colonneCible || str_contains($h, $colonneCible)) {
                $colIndex = $i;
                break;
            }
        }

        if ($colIndex === null) {
            throw new \Exception("Colonne \"$colonneCible\" introuvable dans le fichier.");
        }

        $resultat = [];
        for ($i = 1; $i < count($rows); $i++) {
            $nom = trim((string) ($rows[$i][0] ?? ''));
            if ($nom === '') {
                continue;
            }
            // enlève un éventuel "1. " devant le nom
            $nom = preg_replace('/^\d+\.\s*/', '', $nom);

            $note = $rows[$i][$colIndex] ?? null;
            $note = ($note === '' || $note === null) ? null : (float) str_replace(',', '.', (string) $note);

            $resultat[] = ['nom' => $nom, 'note' => $note];
        }

        return $resultat;
    }

    /**
     * Extraction via l'API Claude (vision) pour PDF/JPEG/PNG.
     * Envoie le fichier + un prompt demandant un JSON strict {nom, note}.
     */
    private function extraireNotesParIA(string $filePath, string $mimeType, string $colonneCible): array
    {
        $base64 = base64_encode(file_get_contents($filePath));

        $contentBlock = $mimeType === 'application/pdf'
            ? ['type' => 'document', 'source' => ['type' => 'base64', 'media_type' => $mimeType, 'data' => $base64]]
            : ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $mimeType, 'data' => $base64]];

        $prompt = "Tu es un assistant qui extrait les données d'une fiche de notes scolaires. "
            . "Le document contient un tableau avec une colonne \"ÉLÈVE\" (noms des élèves) et plusieurs colonnes "
            . "de mois ou de composition. Extrais UNIQUEMENT les valeurs de la colonne \"{$colonneCible}\". "
            . "Réponds STRICTEMENT avec un JSON valide, sans aucun texte avant ou après, sans balises markdown, "
            . "sous la forme exacte : [{\"nom\": \"Nom Prénom\", \"note\": 15.5}, ...]. "
            . "Si une case est vide, illisible, ou barrée, mets note à null. "
            . "N'invente aucune donnée et respecte l'orthographe exacte des noms tels qu'écrits sur le document.";

        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-sonnet-5',
            'max_tokens' => 4000,
            'messages'   => [[
                'role'    => 'user',
                'content' => [$contentBlock, ['type' => 'text', 'text' => $prompt]],
            ]],
        ]);

        if ($response->failed()) {
            $detail = $response->json('error.message') ?? $response->body();
            throw new \Exception("Échec de l'appel à l'API d'extraction (statut {$response->status()}) : {$detail}");
        }

        $data = $response->json();
        $texte = collect($data['content'] ?? [])->firstWhere('type', 'text')['text'] ?? '[]';
        $propre = trim(preg_replace('/```json|```/', '', $texte));

        $resultat = json_decode($propre, true);

        return is_array($resultat) ? $resultat : [];
    }

    /**
     * Rapproche les noms extraits du fichier avec les élèves inscrits de la classe.
     * Retourne une ligne PAR ÉLÈVE INSCRIT (même si non trouvé dans le fichier).
     */
    private function matcherEleves(array $extraits, $eleves): array
    {
        $normaliser = function (string $s): string {
            $s = str_replace(
                ['é', 'è', 'ê', 'ë', 'à', 'â', 'ô', 'î', 'ï', 'ù', 'û', 'ç'],
                ['e', 'e', 'e', 'e', 'a', 'a', 'o', 'i', 'i', 'u', 'u', 'c'],
                mb_strtolower($s)
            );
            return trim(preg_replace('/\s+/', ' ', $s));
        };

        $resultats = [];

        foreach ($eleves as $eleve) {
            $nomComplet = $normaliser($eleve->v_nom . ' ' . $eleve->v_prenom);

            $meilleur = null;
            $meilleurScore = 0;

            foreach ($extraits as $extrait) {
                if (empty($extrait['nom'])) {
                    continue;
                }
                $nomExtrait = $normaliser($extrait['nom']);
                similar_text($nomComplet, $nomExtrait, $pourcentage);

                if ($pourcentage > $meilleurScore) {
                    $meilleurScore = $pourcentage;
                    $meilleur = $extrait;
                }
            }

            $statut = 'non_trouve';
            $note = null;

            if ($meilleur && $meilleurScore >= 85) {
                $statut = 'exact';
                $note = $meilleur['note'];
            } elseif ($meilleur && $meilleurScore >= 60) {
                $statut = 'probable';
                $note = $meilleur['note'];
            }

            $resultats[] = [
                'eleve_id'     => $eleve->i_eleve_id,
                'nom'          => $eleve->v_nom,
                'prenom'       => $eleve->v_prenom,
                'note'         => $note,
                'statut_match' => $statut,
                'nom_extrait'  => $meilleur['nom'] ?? null,
            ];
        }

        return $resultats;
    }
}
