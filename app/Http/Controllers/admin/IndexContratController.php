<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class IndexContratController extends Controller
{
    //
    public function index_contrat(){
        $contrats = DB::table('tblcontrat')
        ->join('tbecole', 'tblcontrat.i_ecole_id', '=', 'tbecole.i_idecole')
        ->join('tbletablissement', 'tblcontrat.i_etablissement_id', '=', 'tbletablissement.i_idetablissement')
        ->select(
            'tblcontrat.*',
            'tbecole.v_nomecole',
            'tbletablissement.v_nometablissement'
        )
        ->orderBy('tblcontrat.i_contrat_id', 'desc')
        ->get();
        return view('admin.contrat.index', compact('contrats'));
    }

    public function toggleStatus($id)
    {
        $contrat = DB::table('tblcontrat')
            ->where('i_contrat_id', $id)
            ->first();

        if (!$contrat) {

            return back()->with(
                'error',
                'Contrat introuvable.'
            );
        }

        DB::table('tblcontrat')
            ->where('i_contrat_id', $id)
            ->update([
                'b_desabled' => $contrat->b_desabled == 1 ? 0 : 1
            ]);

        return back()->with(
            'success',
            $contrat->b_desabled == 1
                ? 'Contrat suspendu avec succès.'
                : 'Contrat activé avec succès.'
        );
    }
}
