<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SettingClassesController extends Controller
{
    //
    public function setting_classes($slug){
        abort_unless(PermissionHelper::hasRoute('setting.classes'),403);

        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        $data_classe = DB::table('tblclasse')
            ->where('tblclasse.i_ecole_id', session('ecole_id'))
            ->join('tblniveau','tblniveau.i_niveauID','tblclasse.i_niveau_id')
            ->get();
        $data_anneescolaire = DB::table('tblanneesclaire')->orderBy('i_idanneesclaire','desc')->get();

        // Année scolaire en cours (la plus récente)
        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;

        if ($annee_courante) {
            $this->insererModalitesSiAbsentes($annee_courante);
        }

        return view('ecoles.classe.setting_classes', compact('slug','data_classe','data_anneescolaire','annee_courante'));
    }

    // Insérer les modalités manquantes avec prix à 0
    private function insererModalitesSiAbsentes($annee)
    {
        $ecole_id = session('ecole_id');
        $classes = DB::table('tblclasse')->where('i_ecole_id', $ecole_id)->get();

        foreach ($classes as $classe) {
            $existe = DB::table('tblmodaliteclasse')
                ->where('i_ecoleId', $ecole_id)
                ->where('i_classeId', $classe->i_classe_id) // adapte le nom du champ PK
                ->where('v_anneescolaire', $annee)
                ->exists();

            if (!$existe) {
                DB::table('tblmodaliteclasse')->insert([
                    'i_ecoleId'            => $ecole_id,
                    'i_classeId'           => $classe->i_classe_id,
                    'v_anneescolaire'      => $annee,
                    'd_pirx_inscription'   => 0,
                    'd_prix_reinscription' => 0,
                    'd_prix_mensuelle'     => 0,  // ← ajouté
                    'd_tranche1'           => 0,
                    'd_tranche2'           => 0,
                    'd_tranche3'           => 0,
                    'd_tranche_annuelle'   => 0,
                    'i_userId'             => Auth::id(),
                ]);
            }
        }
    }

    // Route AJAX : récupérer les données par année
public function getModalitesByAnnee(Request $request, $slug)
{
    $ecole_id = session('ecole_id');
    $annee    = $request->annee;

    $this->insererModalitesSiAbsentes($annee);

    $data = DB::table('tblmodaliteclasse as m')
        ->join('tblclasse as c', 'c.i_classe_id', '=', 'm.i_classeId')
        ->join('tblniveau as n', 'n.i_niveauID', '=', 'c.i_niveau_id')
        ->where('m.i_ecoleId', $ecole_id)
        ->where('m.v_anneescolaire', $annee)
        ->select('m.*', 'c.v_nom_classe as classe', 'n.v_niveaux as niveau') // ← v_niveaux
        ->get();

    return response()->json($data);
}

    // Route AJAX : mettre à jour une modalité
    public function updateModalite(Request $request)
    {
        $request->validate([
            'id'    => 'required|integer',
            'champ' => 'required|string|in:d_pirx_inscription,d_prix_reinscription,d_prix_mensuelle,d_tranche1,d_tranche2,d_tranche3,d_tranche_annuelle',
            'valeur'=> 'required|numeric|min:0',
        ]);

        DB::table('tblmodaliteclasse')
            ->where('i_modalite_classe', $request->id)
            ->where('i_ecoleId', session('ecole_id'))
            ->update([$request->champ => $request->valeur]);

        return response()->json(['success' => true]);
    }


}
