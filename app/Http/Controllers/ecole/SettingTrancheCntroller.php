<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SettingTrancheCntroller extends Controller
{
    //
    public function setting_tranche($slug){
        abort_unless(PermissionHelper::hasRoute('setting.tranche'),403);
        $ecole = DB::table('tbecole')->where('v_slugecole', $slug)->first();
        $data_anneescolaire = DB::table('tblanneesclaire')->orderBy('i_idanneesclaire','desc')->get();
        // Année scolaire en cours (la plus récente)
        $annee_courante = $data_anneescolaire->first()->v_annesclaire ?? null;
        return view('ecoles.classe.setting_tranche',compact('slug','annee_courante','data_anneescolaire'));
    }

    public function getTranches(Request $request, $slug)
    {
        $ecole_id = session('ecole_id');
        $annee    = $request->annee;

        $mois = ['janvier','fevrier','mars','avril','mai','juin',
                'juillet','aout','septembre','octobre','novembre','decembre'];
        $tranches = ['1ere', '2eme', '3eme', 'annuelle'];

        // Insérer les lignes manquantes
        foreach ($tranches as $tranche) {
            foreach ($mois as $m) {
                $existe = DB::table('tbltranche')
                    ->where('i_ecole_id', $ecole_id)
                    ->where('v_anneescolaire', $annee)
                    ->where('v_tranche', $tranche)
                    ->where('v_mois', $m)
                    ->exists();

                if (!$existe) {
                    DB::table('tbltranche')->insert([
                        'i_ecole_id'      => $ecole_id,
                        'v_anneescolaire' => $annee,
                        'v_tranche'       => $tranche,
                        'v_mois'          => $m,
                        'b_actif'         => 0,
                        'i_user_id'       => Auth::id(),
                    ]);
                }
            }
        }

        $data = DB::table('tbltranche')
            ->where('i_ecole_id', $ecole_id)
            ->where('v_anneescolaire', $annee)
            ->get();

        return response()->json($data);
    }

    public function updateTranche(Request $request, $slug)
    {
        $request->validate([
            'id'     => 'required|integer',
            'actif'  => 'required|boolean',
        ]);

        DB::table('tbltranche')
            ->where('i_tranche_id', $request->id)
            ->where('i_ecole_id', session('ecole_id'))
            ->update(['b_actif' => $request->actif]);

        return response()->json(['success' => true]);
    }
}
