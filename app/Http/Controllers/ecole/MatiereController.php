<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MatiereController extends Controller
{
    public function matiere($slug)
    {
        return view('ecoles.matiere.matiere', compact('slug'));
    }

    public function store(Request $request, $slug)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'v_code'             => 'nullable|string|max:50',
            'v_libelle'          => 'required|string|max:150',
            'i_coefficient'      => 'required|integer|min:1',
            'v_couleur'          => 'nullable|string|max:20',
            't_details_matiere'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation',
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Récupération de l'école et de l'utilisateur connecté
            $i_ecole_id = session('ecole_id');   // à adapter selon ta gestion de session
            $i_user_id  = auth()->id();

            // Vérifier si le libellé existe déjà pour cette école
            $exists = DB::table('tbmatiere')
                ->where('v_libelle', $request->v_libelle)
                ->where('i_ecole_id', $i_ecole_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Cette matière existe déjà.'
                ]);
            }

            DB::table('tbmatiere')->insert([
                'v_code'            => $request->v_code,
                'v_libelle'         => $request->v_libelle,
                'i_coefficient'     => $request->i_coefficient,
                'v_couleur'         => $request->v_couleur,
                'i_ecole_id'        => $i_ecole_id,
                'd_datecreation'    => now(),
                'i_user_id'         => $i_user_id,
                'b_desabled'        => 1,
                't_details_matiere' => $request->t_details_matiere,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Matière enregistrée avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }
}
