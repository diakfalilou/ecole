<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use App\Services\TableActionService;

class IndexEleveController extends Controller
{
    //
    public function index_eleves($slug){
        // abort_unless(PermissionHelper::hasRoute('index.eleves'),403);
        $actionsEleve = TableActionService::getActionsByMenu('Élèves');
        $eleves = DB::table('tbleleve')
        ->leftJoin('tblparent', 'tbleleve.i_parenti_id', '=', 'tblparent.i_parent_id')
        ->leftJoin('tbleleve_medical', 'tbleleve.i_eleve_id', '=', 'tbleleve_medical.i_eleve_id')
        ->where('tbleleve.i_ecole_id', session('ecole_id'))
        ->select(
            'tbleleve.*',
            'tblparent.v_nom_tuteur as parent_name'
        )
        ->get();
        return view('ecoles.eleves.index',compact('eleves','slug','actionsEleve'));
    }
}

