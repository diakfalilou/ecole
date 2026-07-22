<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\TableActionService;

class ListeInscriReinscritController extends Controller
{
    //
    public function inscription_reinscription($slug){
        // dd($slug);
        abort_unless(PermissionHelper::hasRoute('liste.inscription.reinscription'),403);
        $actionsEleve = TableActionService::getActionsByMenu('Inscription/Reinscription');
        // dd($actionsEleve);
        $eleves = DB::table('tblinscription')
        ->leftJoin('tbleleve', 'tbleleve.i_eleve_id', '=', 'tblinscription.i_eleve_id')
        ->leftJoin('tblniveau', 'tblniveau.i_niveauID', '=', 'tblinscription.i_niveau_id')
        ->leftJoin('tblclasse', 'tblclasse.i_classe_id', '=', 'tblinscription.i_classe_id')
        ->leftJoin('tblparent', 'tbleleve.i_parenti_id', '=', 'tblparent.i_parent_id')
        ->leftJoin('tbleleve_medical', 'tbleleve.i_eleve_id', '=', 'tbleleve_medical.i_eleve_id')
        ->where('tbleleve.i_ecole_id', session('ecole_id'))
        ->select(
            'tbleleve.*',
            'tblinscription.v_typeinscription as v_typeinscription',
            'tblparent.v_nom_tuteur as parent_name',
            'tblparent.v_nom_tuteur as v_nom_tuteur',
            'tblparent.v_telephone_tuteur as v_telephone_tuteur',


            'tblniveau.v_niveaux as v_niveaux',
            'tblclasse.v_nom_classe as v_nom_classe',

        )
        ->get();

        // dd($eleves);
        return view('ecoles.inscription_reinscription.index',compact('slug','eleves','actionsEleve'));
    }
}
