<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexEtablissementController extends Controller
{
    //
    public function index_etablissement(){
        $etablissements = DB::table('tbletablissement')->orderBy('v_nometablissement', 'asc')->get();
        return view('admin.etablissement.index',compact('etablissements'));
    }
}
