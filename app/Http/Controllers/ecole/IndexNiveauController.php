<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexNiveauController extends Controller
{
    public function index_niveau($slug)
    {
        $ecole = DB::table('tbecole')
        ->where('v_slugecole', $slug)
        ->first();

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view('ecoles.classe.niveau', compact(
            'slug',
            'ecole',
            'niveaux'
        ));
    }

    public function store(Request $request, $slug)
    {
        $request->validate([
            'niveau' => ['required']
        ]);

        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        if (!$ecole) {
            return back()->with('error', 'Ecole introuvable.');
        }

        $niveauExiste = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('v_niveaux', $request->niveau)
            ->exists();

        if ($niveauExiste) {
            return back()->with(
                'error',
                'Ce niveau existe déjà dans cette école.'
            );
        }

        DB::table('tblniveau')->insert([
            'v_niveaux'      => $request->niveau,
            'i_ecole_id'     => $ecole->i_idecole,
            'i_userID'       => auth()->id(),
            'd_creationdate' => now(),
            'b_desabled'     => 1
        ]);

        return back()->with(
            'success',
            'Niveau ajouté avec succès.'
        );
    }

    public function update(Request $request,$slug,$id){
        $request->validate([
            'niveau' => 'required'
        ]);

        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        if (!$ecole) {
            return back()->with(
                'error',
                'Ecole introuvable.'
            );
        }

        $existe = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('v_niveaux', $request->niveau)
            ->where('i_niveauID', '!=', $id)
            ->exists();

        if ($existe) {

            return back()->with(
                'error',
                'Ce niveau existe déjà dans cette école.'
            );
        }

        DB::table('tblniveau')
            ->where('i_niveauID', $id)
            ->update([
                'v_niveaux' => $request->niveau
            ]);

        return back()->with(
            'success',
            'Niveau modifié avec succès.'
        );
    }

    public function toggleStatus(Request $request,$slug,$id){
        $niveau = DB::table('tblniveau')
            ->where('i_niveauID', $id)
            ->first();

        if (!$niveau) {

            return back()->with(
                'error',
                'Niveau introuvable.'
            );
        }

        DB::table('tblniveau')
            ->where('i_niveauID', $id)
            ->update([
                'b_desabled' =>
                    $niveau->b_desabled == 1
                    ? 0
                    : 1
            ]);

        return back()->with(
            'success',
            $niveau->b_desabled == 1
                ? 'Niveau suspendu avec succès.'
                : 'Niveau activé avec succès.'
        );
    }
}
