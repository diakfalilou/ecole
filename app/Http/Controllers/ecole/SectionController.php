<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function index($slug)
    {
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        if (!$ecole) {
            abort(404);
        }

        $sections = DB::table('tblsection')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
            ->get();

        return view(
            'ecoles.classe.sections',
            compact(
                'slug',
                'ecole',
                'sections'
            )
        );
    }

    public function store(Request $request, $slug)
    {
        $request->validate([
            'section' => 'required|max:222'
        ]);

        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        $existe = DB::table('tblsection')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('v_sections', $request->section)
            ->exists();

        if ($existe) {

            return back()->with(
                'error',
                'Cette section existe déjà.'
            );
        }

        DB::table('tblsection')->insert([
            'v_sections'      => $request->section,
            'i_userID'        => auth()->id(),
            'i_ecole_id'      => $ecole->i_idecole,
            'd_creationdate'  => now(),
            'b_desabled'      => 1
        ]);

        return back()->with(
            'success',
            'Section ajoutée avec succès.'
        );
    }

    public function update(
        Request $request,
        $slug,
        $id
    )
    {
        $request->validate([
            'section' => 'required|max:222'
        ]);

        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->first();

        $existe = DB::table('tblsection')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->where('v_sections', $request->section)
            ->where('i_niveauID', '!=', $id)
            ->exists();

        if ($existe) {

            return back()->with(
                'error',
                'Cette section existe déjà.'
            );
        }

        DB::table('tblsection')
            ->where('i_niveauID', $id)
            ->update([
                'v_sections' => $request->section
            ]);

        return back()->with(
            'success',
            'Section modifiée avec succès.'
        );
    }

    public function toggleStatus(
        Request $request,
        $slug,
        $id
    )
    {
        $section = DB::table('tblsection')
            ->where('i_niveauID', $id)
            ->first();

        if (!$section) {

            return back()->with(
                'error',
                'Section introuvable.'
            );
        }

        DB::table('tblsection')
            ->where('i_niveauID', $id)
            ->update([
                'b_desabled' =>
                    $section->b_desabled == 1
                    ? 0
                    : 1
            ]);

        return back()->with(
            'success',
            $section->b_desabled == 1
                ? 'Section suspendue avec succès.'
                : 'Section activée avec succès.'
        );
    }
}
