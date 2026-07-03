<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DetailEleveController extends Controller
{
    public function detail_eleve($slug, $id)
    {
        $eleve = DB::table('tbleleve')
            ->where('tbleleve.i_eleve_id', $id)
            ->join('tblclasse', 'tblclasse.i_classe_id','tbleleve.i_classe_id')
            ->first();
        // dd($eleve);

        return view('ecoles.eleves.detail_eleve', compact('slug', 'eleve'));
    }
}
