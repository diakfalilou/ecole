<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasseController extends Controller
{
    //
    public function classes($slug){

        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        if (!$ecole) {
            abort(404);
        }
        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_desabled',1)
            ->orderBy('i_niveauID', 'desc')
        ->get();

       $classes = DB::table('tblclasse')
            ->join('tblniveau', 'tblclasse.i_niveau_id', '=', 'tblniveau.i_niveauID')
            ->leftJoin('tblsection', 'tblclasse.i_section_id', '=', 'tblsection.i_niveauID')
            ->where('tblclasse.i_ecole_id', $ecole->i_idecole)
            ->select(
                'tblclasse.*',
                'tblniveau.v_niveaux',
                'tblsection.v_sections'
            )
            ->orderBy('tblclasse.i_classe_id', 'desc')
            ->get();



        $sections = DB::table('tblsection')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('b_desabled',1)
            ->orderBy('i_niveauID', 'desc')
        ->get();

        return view(
            'ecoles.classe.classe',
            compact(
                'slug',
                'ecole',
                'classes',
                'niveaux',
                'sections'
            )
        );
    }

    public function store(Request $request, $slug){
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        if (!$ecole) {
            abort(404);
        }

        // 🔥 VERIFICATION DOUBLON
        $exists = DB::table('tblclasse')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('v_nom_classe', $request->v_nom_classe)
            ->first();

        if ($exists) {
            return back()->with('error', 'Cette classe existe déjà dans cette école.');
        }

        DB::table('tblclasse')->insert([
            'v_nom_classe' => $request->v_nom_classe,
            'i_capacite' => $request->i_capacite,
            'i_ecole_id' => $ecole->i_idecole,
            'i_niveau_id' => $request->i_niveau_id,
            'i_section_id' => $request->i_section_id,
            't_detail_classe' => $request->t_detail_classe,
            'i_user_id' => auth()->id(),
            'b_desabled' => 1,
            'd_datecreation' => now(),
        ]);

        return back()->with('success', 'Classe ajoutée avec succès.');
    }

    public function update(Request $request, $slug, $id)
    {
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        if (!$ecole) {
            abort(404);
        }

        // anti doublon
        $exists = DB::table('tblclasse')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('v_nom_classe', $request->v_nom_classe)
            ->where('i_classe_id', '!=', $id)
            ->first();

        if ($exists) {
            return back()->with('error', 'Cette classe existe déjà.');
        }

        DB::table('tblclasse')
            ->where('i_classe_id', $id)
            ->update([
                'v_nom_classe' => $request->v_nom_classe,
                'i_capacite' => $request->i_capacite,
                'i_niveau_id' => $request->i_niveau_id,
                'i_section_id' => $request->i_section_id,
                't_detail_classe' => $request->t_detail_classe,
                'i_user_id' => auth()->id(),
            ]);

        return back()->with('success', 'Classe modifiée avec succès.');
    }

    public function toggleStatus($slug, $id){
        $classe = DB::table('tblclasse')
            ->where('i_classe_id', $id)
            ->first();

        if (!$classe) {
            return back()->with('error', 'Classe introuvable.');
        }

        DB::table('tblclasse')
            ->where('i_classe_id', $id)
            ->update([
                'b_desabled' => $classe->b_desabled == 1 ? 0 : 1
            ]);

        return back()->with('success', 'Statut de la classe mis à jour.');
    }
}
