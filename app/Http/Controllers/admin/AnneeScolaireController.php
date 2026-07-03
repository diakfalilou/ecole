<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnneeScolaireController extends Controller
{

    public function annee_scolaire()
    {

        $annees = DB::table('tblanneesclaire')
            ->orderBy('i_idanneesclaire', 'DESC')
            ->get();

        return view('admin.anneescolaie.index', compact('annees'));
    }

    public function store(Request $request)
    {

            $request->validate([

            'v_annesclaire' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                'max:9',
                'unique:tblanneesclaire,v_annesclaire'
            ],

            'v_debutanneesclaire' => [
                'required',
                'digits:4'
            ],

            'v_finanneesclaire' => [
                'required',
                'digits:4'
            ],

            'd_dateexpirationanneescolaire' => [
                'required',
                'date'
            ],

        ], [

            // =========================
            // Année scolaire
            // =========================

            'v_annesclaire.required' => 'L’année scolaire est obligatoire.',

            'v_annesclaire.regex' => 'Le format doit être : 2025-2026',

            'v_annesclaire.max' => 'L’année scolaire ne doit pas dépasser 9 caractères.',

            'v_annesclaire.unique' => 'Cette année scolaire existe déjà.',

            // =========================
            // Début
            // =========================

            'v_debutanneesclaire.required' => 'L’année de début est obligatoire.',

            'v_debutanneesclaire.digits' => 'L’année de début doit contenir exactement 4 chiffres.',

            // =========================
            // Fin
            // =========================

            'v_finanneesclaire.required' => 'L’année de fin est obligatoire.',

            'v_finanneesclaire.digits' => 'L’année de fin doit contenir exactement 4 chiffres.',

            // =========================
            // Expiration
            // =========================

            'd_dateexpirationanneescolaire.required' => 'La date d’expiration est obligatoire.',

            'd_dateexpirationanneescolaire.date' => 'La date d’expiration est invalide.',

        ]);


        DB::table('tblanneesclaire')->insert([
            'v_annesclaire' => $request->v_annesclaire,
            'v_debutanneesclaire' => $request->v_debutanneesclaire,
            'v_finanneesclaire' => $request->v_finanneesclaire,
            'd_dateexpirationanneescolaire' => $request->d_dateexpirationanneescolaire,
            'd_datecreationanneesclaire' => now(),
            'i_userID' => session('user_id'),
        ]);


        flash()->success('Année scolaire ajoutée avec succès.');

        return back();

    }

    public function destroy($id)
    {

        DB::table('tblanneesclaire')
            ->where('i_idanneesclaire', $id)
            ->delete();

        flash()->success('Année scolaire supprimée avec succès.');

        return back();

}

}
