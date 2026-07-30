<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ElevesImport;

class ChargerListeEleveController extends Controller
{
    private function getEcole(string $slug)
    {
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        abort_unless($ecole, 404, 'École introuvable.');
        return $ecole;
    }

    public function charger_liste_eleve($slug)
    {
        abort_unless(PermissionHelper::hasRoute('notes'), 403);

        $ecole = $this->getEcole($slug);

        $data_anneescolaire = DB::table('tblcontrat')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_contrat_id', 'desc')
            ->get();

        $annee_courante = $data_anneescolaire->first()->v_annee_scolaire ?? null;

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.eleves.charger_liste_eleve', compact('niveaux', 'data_anneescolaire', 'annee_courante'));
    }

    public function getClassesByNiveau($slug, $niveauId)
    {
        $ecole = $this->getEcole($slug);

        $classes = DB::table('tblclasse')
            ->where('i_niveau_id', $niveauId)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_desabled', 1)
            ->select('i_classe_id', 'v_nom_classe')
            ->orderBy('v_nom_classe')
            ->get();

        return response()->json($classes);
    }

    public function getSectionsByNiveau($slug, $niveauId)
    {
        $ecole = $this->getEcole($slug);

        $sections = DB::table('tblsection')
            ->where('i_niveauID', $niveauId)
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_desabled', 1)
            ->select('i_niveauID as id', 'v_sections')
            ->get();

        return response()->json($sections);
    }

    /**
     * Étape 1 : upload -> parsing -> insertion dans la table de staging tbl_import_eleve
     */
    public function previewExcel(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'fichier_excel'    => 'required|file|mimes:xlsx,xls,csv',
            'niveau_id'        => 'required|integer',
            'classe_id'        => 'required|integer',
            'section_id'       => 'nullable|integer',
            'annee_scolaire'   => 'required|string',
            'type_inscription' => 'required|in:inscription,reinscription',
        ]);

        $data = Excel::toArray(new ElevesImport, $request->file('fichier_excel'));
        $rows = $data[0] ?? [];

        if (count($rows) < 2) {
            return response()->json(['message' => 'Le fichier est vide ou ne contient pas de données.'], 422);
        }

        $headers = array_map(function ($h) {
            return (string) Str::of((string) $h)->trim()->lower()->ascii();
        }, $rows[0]);

        $map = $this->mapHeaders($headers);
        $userId = Auth::id();
        $batchId = (string) Str::uuid();

        $inserted = [];

        DB::transaction(function () use ($rows, $map, $ecole, $request, $userId, $batchId, &$inserted) {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;

                $nom            = isset($map['nom']) ? trim((string)($row[$map['nom']] ?? '')) : '';
                $prenom         = isset($map['prenom']) ? trim((string)($row[$map['prenom']] ?? '')) : '';
                $matricule      = isset($map['matricule']) ? trim((string)($row[$map['matricule']] ?? '')) : '';
                $sexe           = isset($map['sexe']) ? trim((string)($row[$map['sexe']] ?? '')) : '';
                $contact_parent = isset($map['contact_parent']) ? trim((string)($row[$map['contact_parent']] ?? '')) : '';

                if ($nom === '' && $prenom === '') continue;

                // Détection de doublon dans tbleleve : matricule d'abord, sinon nom + prénom
                $existant = null;
                if ($matricule !== '') {
                    $existant = DB::table('tbleleve')
                        ->where('i_ecole_id', $ecole->i_idecole)
                        ->where('v_matricule', $matricule)
                        ->first();
                }
                if (!$existant && $nom !== '' && $prenom !== '') {
                    $existant = DB::table('tbleleve')
                        ->where('i_ecole_id', $ecole->i_idecole)
                        ->whereRaw('LOWER(v_nom) = ?', [mb_strtolower($nom)])
                        ->whereRaw('LOWER(v_prenom) = ?', [mb_strtolower($prenom)])
                        ->first();
                }

                $importId = DB::table('tbl_import_eleve')->insertGetId([
                    'v_batch_id'          => $batchId,
                    'i_ecole_id'          => $ecole->i_idecole,
                    'i_niveau_id'         => $request->niveau_id,
                    'i_classe_id'         => $request->classe_id,
                    'i_section_id'        => $request->section_id,
                    'v_annee_scolaire'    => $request->annee_scolaire,
                    'v_type_inscription'  => $request->type_inscription,
                    'v_nom'               => $nom,
                    'v_prenom'            => $prenom,
                    'v_matricule'         => $matricule,
                    'v_sexe'              => $sexe,
                    'v_contact_parent'    => $contact_parent,
                    'b_doublon'           => $existant ? 1 : 0,
                    'i_eleve_id_existant' => $existant->i_eleve_id ?? null,
                    'v_action'            => $existant ? 'existant' : 'nouveau',
                    'i_userID'            => $userId,
                    'd_creationdate'      => now(),
                ]);

                $inserted[] = [
                    'import_id'         => $importId,
                    'nom'               => $nom,
                    'prenom'            => $prenom,
                    'matricule'         => $matricule,
                    'sexe'              => $sexe,
                    'contact_parent'    => $contact_parent,
                    'doublon'           => (bool) $existant,
                    'eleve_id_existant' => $existant->i_eleve_id ?? null,
                    'action'            => $existant ? 'existant' : 'nouveau',
                ];
            }
        });

        return response()->json([
            'batch_id' => $batchId,
            'rows'     => $inserted,
        ]);
    }

    private function mapHeaders(array $headers): array
    {
        $map = [];
        foreach ($headers as $index => $h) {
            $h = str_replace(['é', 'è', 'ê'], 'e', $h);
            if (str_contains($h, 'matricule')) $map['matricule'] = $index;
            elseif (str_contains($h, 'nom') && !str_contains($h, 'prenom')) $map['nom'] = $index;
            elseif (str_contains($h, 'prenom')) $map['prenom'] = $index;
            elseif (str_contains($h, 'sexe')) $map['sexe'] = $index;
            elseif (str_contains($h, 'contact') || str_contains($h, 'tel')) $map['contact_parent'] = $index;
        }
        return $map;
    }

    /**
     * Étape 2 : transfert staging -> tbleleve (si nouveau) + tblinscription
     */
    public function saveInscriptions(Request $request, $slug)
    {
        $ecole = $this->getEcole($slug);

        $request->validate([
            'batch_id'                 => 'required|string',
            'rows'                     => 'required|array|min:1',
            'rows.*.import_id'         => 'required|integer',
            'rows.*.nom'               => 'required|string',
            'rows.*.prenom'            => 'required|string',
            'rows.*.matricule'         => 'nullable|string',
            'rows.*.sexe'              => 'nullable|string',
            'rows.*.contact_parent'    => 'nullable|string',
            'rows.*.action'            => 'required|in:nouveau,existant,ignorer',
            'rows.*.eleve_id_existant' => 'nullable|integer',
        ]);

        $userId = Auth::id();
        $inseres = 0;
        $ignores = 0;

        DB::transaction(function () use ($request, $ecole, $userId, &$inseres, &$ignores) {
            foreach ($request->rows as $row) {
                $importRow = DB::table('tbl_import_eleve')
                    ->where('i_import_id', $row['import_id'])
                    ->where('v_batch_id', $request->batch_id)
                    ->where('i_ecole_id', $ecole->i_idecole)
                    ->first();

                if (!$importRow) continue;

                DB::table('tbl_import_eleve')
                    ->where('i_import_id', $row['import_id'])
                    ->update([
                        'v_nom'            => $row['nom'],
                        'v_prenom'         => $row['prenom'],
                        'v_matricule'      => $row['matricule'] ?? null,
                        'v_sexe'           => $row['sexe'] ?? null,
                        'v_contact_parent' => $row['contact_parent'] ?? null,
                        'v_action'         => $row['action'],
                    ]);

                if ($row['action'] === 'ignorer') {
                    $ignores++;
                    continue;
                }

                if ($row['action'] === 'nouveau') {
                    // Créer une fiche parent minimale à partir du contact fourni dans le fichier Excel
                    $parentId = DB::table('tblparent')->insertGetId([
                        'v_tuteur_type'      => 'autre',
                        'v_telephone_tuteur' => $row['contact_parent'] ?? null,
                        'i_ecole_id'         => $ecole->i_idecole,
                        'i_user_id'          => $userId,
                        'd_datecreation'     => now(),
                        'b_desabled'         => 1,
                    ]);

                    $eleveId = DB::table('tbleleve')->insertGetId([
                        'v_nom'            => $row['nom'],
                        'v_prenom'         => $row['prenom'],
                        'v_matricule'      => $row['matricule'] ?? null,
                        'v_genre'          => $row['sexe'] ?? null,
                        'v_telephone'      => $row['contact_parent'] ?? null,
                        'd_date_naissance' => '1900-01-01', // valeur par défaut - non fournie par le fichier Excel
                        'i_ecole_id'       => $ecole->i_idecole,
                        'b_desabled'       => 1,
                        'i_user_id'        => $userId,
                        'd_datecreation'   => now(),
                        'i_parenti_id'     => $parentId,
                    ]);
                } else { // existant
                    $eleveId = $row['eleve_id_existant'];
                }

                DB::table('tblinscription')->updateOrInsert(
                    [
                        'i_eleve_id'       => $eleveId,
                        'i_ecole_id'       => $ecole->i_idecole,
                        'v_annee_scolaire' => $importRow->v_annee_scolaire,
                    ],
                    [
                        'i_niveau_id'        => $importRow->i_niveau_id,
                        'i_classe_id'        => $importRow->i_classe_id,
                        'i_section_id'       => $importRow->i_section_id,
                        'v_typeinscription'  => $importRow->v_type_inscription,
                        'i_user_id'          => $userId,
                        'b_statut'           => 1,
                        'b_active'           => 1,
                        'd_date_inscription' => now(),
                    ]
                );

                DB::table('tbl_import_eleve')
                    ->where('i_import_id', $row['import_id'])
                    ->update(['b_traite' => 1]);

                $inseres++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$inseres} élève(s) inscrit(s), {$ignores} ignoré(s).",
        ]);
    }
}
