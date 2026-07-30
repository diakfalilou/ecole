<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChargerNoteController extends Controller
{
    //

    public function charger_note($slug){

        return view('ecoles.notes.charger_note');
    }
}
