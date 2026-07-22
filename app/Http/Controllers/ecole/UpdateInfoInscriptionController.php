<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateInfoInscriptionController extends Controller
{
    //
    public function update_inscription($slug, $eleveId){
        $ecole = DB::table('tbecole')
        ->where('v_slugecole', $slug)
        ->first();
        $eleve = DB::table('tblinscription')
            ->where('tblinscription.i_eleve_id', $eleveId)
            ->join('tbleleve', 'tbleleve.i_eleve_id','tblinscription.i_eleve_id')
            ->join('tblniveau', 'tblniveau.i_niveauID','tblinscription.i_niveau_id')
            ->join('tblclasse', 'tblclasse.i_classe_id','tblinscription.i_classe_id')
        ->first();
        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
        ->get();
        // dd($eleve);
        return view('ecoles.inscription_reinscription.updateinscription',compact('slug','eleve','niveaux','eleveId'));
    }

    public function store_update(Request $request,$eleveId){
        // dd($request);
        DB::table('tblinscription')
            ->where('i_eleve_id', $request->eleveId)
            ->update([
                'i_niveau_id'=>$request->niveau_id,
                'i_classe_id'=>$request->classe_id
            ]);
        return back()->withInput()->with('success', 'Muttation de l\'eleve effectuez avec succée.');

    }
}
