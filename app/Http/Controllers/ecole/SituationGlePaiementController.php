<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SituationGlePaiementController extends Controller
{
    //
    public function situation_gen_paiement($slug){

        return view('ecoles.comptabilite.situation_general_classe');
    }
}
