<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExonorationController extends Controller
{
    public function exoneration($slug)
    {

        abort_unless(PermissionHelper::hasRoute('exoneration'), 403);
        $ecole              = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        $data_anneescolaire = DB::table('tblanneesclaire')->orderBy('i_idanneesclaire', 'desc')->get();
        $annee_courante     = $data_anneescolaire->first()->v_annesclaire ?? null;
        $niveaux            = DB::table('tblniveau')
                                ->where('i_ecole_id', $ecole->i_idecole)
                                ->orderBy('i_niveauID', 'desc')
                                ->get();
        return view('ecoles.comptabilite.exoneration', compact('data_anneescolaire', 'annee_courante', 'niveaux', 'slug'));
    }

    public function getModaliteExoneration(Request $request)
    {
        $modalite = DB::table('tblmodaliteclasse')
            ->where('i_classeId', $request->classe_id)
            ->where('v_anneescolaire', $request->annee_scolaire)
            ->first();

        if (!$modalite) {
            return response()->json(['error' => 'Aucune modalité trouvée pour cette classe.'], 404);
        }

        return response()->json($modalite);
    }

    public function getHistoriqueExoneration($slug, $eleveId, Request $request)
    {
        $anneeScolaire = $request->annee_scolaire;

        $exonerations = DB::table('tblexoneration')
            ->where('i_eleve_id', $eleveId)
            ->when($anneeScolaire, fn($q) => $q->where('v_annee_scolaire', $anneeScolaire))
            ->orderBy('d_datecreation', 'desc')
            ->get();

        $result = $exonerations->map(fn($e) => [
            'date'           => \Carbon\Carbon::parse($e->d_datecreation)->format('d/m/Y'),
            'mode'           => $e->v_mode_paiement,
            'type'           => $e->v_type_exoneration,
            'montant_initial'=> $e->f_montant_initial,
            'montant_exonere'=> $e->f_montant_exonere,
            'pourcentage'    => $e->f_pourcentage,
            'motif'          => $e->t_motif,
            'autorise_par'   => $e->v_autorise_par,
            'statut'         => $e->v_statut,
        ]);

        return response()->json($result);
    }

    public function enregistrerExoneration(Request $request)
    {
        $request->validate([
            'eleve_id'        => 'required|integer',
            'classe_id'       => 'required|integer',
            'annee_scolaire'  => 'required|string',
            'mode_paiement'   => 'required|in:inscription,reinscription,1er_tranche,2eme_tranche,3eme_tranche,annuelle',
            'type_exoneration'=> 'required|in:partielle,totale',
            'pourcentage'     => 'required|numeric|min:1|max:100',
            'montant_initial' => 'required|numeric|min:0',
            'montant_exonere' => 'required|numeric|min:0',
            'motif'           => 'required|string|max:1000',
            'date_exoneration'=> 'required|date',
            'autorise_par'    => 'required|string|max:150',
            'justificatif'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'slug'            => 'required|string',
        ]);

        $ecole = DB::table('tbecole')->where('v_slugecole', $request->slug)->first();
        if (!$ecole) return response()->json(['success' => false, 'message' => 'École introuvable.'], 404);

        // Vérifier si une exonération existe déjà pour ce mode/élève/année
        $existeDeja = DB::table('tblexoneration')
            ->where('i_eleve_id', $request->eleve_id)
            ->where('v_annee_scolaire', $request->annee_scolaire)
            ->where('v_mode_paiement', $request->mode_paiement)
            ->where('v_statut', 'active')
            ->exists();

        if ($existeDeja) {
            return response()->json([
                'success' => false,
                'message' => 'Une exonération active existe déjà pour ce mode de paiement cette année.'
            ], 422);
        }

        $cheminJustificatif = null;
        if ($request->hasFile('justificatif')) {
            $cheminJustificatif = $request->file('justificatif')->store('exonerations', 'public');
        }

        try {
            DB::table('tblexoneration')->insert([
                'i_ecole_id'         => $ecole->i_idecole,
                'i_eleve_id'         => $request->eleve_id,
                'i_classe_id'        => $request->classe_id,
                'v_annee_scolaire'   => $request->annee_scolaire,
                'v_mode_paiement'    => $request->mode_paiement,
                'v_type_exoneration' => $request->type_exoneration,
                'f_pourcentage'      => $request->pourcentage,
                'f_montant_initial'  => $request->montant_initial,
                'f_montant_exonere'  => $request->montant_exonere,
                't_motif'            => $request->motif,
                'd_date_exoneration' => $request->date_exoneration,
                'v_autorise_par'     => $request->autorise_par,
                'v_justificatif'     => $cheminJustificatif,
                'i_created_by'       => Auth::id(),
                'v_statut'           => 'active',
                'd_datecreation'     => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Exonération enregistrée avec succès.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
