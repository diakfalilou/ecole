<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AddContratController extends Controller
{
    //
    public function add_contrat(){
        $anneescolaires=DB::table('tblanneesclaire')
        ->orderBy('i_idanneesclaire','Desc')
        ->get();
        $etablissemnt=DB::table('tbletablissement')
        ->orderBy('i_idetablissement','desc')
        ->get();

        return view('admin.contrat.add',compact('anneescolaires','etablissemnt'));
    }

    public function getEcoles($idEtablissement)
    {
        $ecoles = DB::table('tbecole')
            ->where('i_idetablissement', $idEtablissement)
            ->where('bt_etat_ecole', 1)
            ->orderBy('v_nomecole')
            ->get();

        return response()->json($ecoles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'annee_scolaire' => 'required',
            'etablissement_id' => 'required',
            'ecole_id' => 'required',
            'datedebut' => 'required|date',
            'datefin' => 'required|date|after_or_equal:datedebut',
            'montant' => 'required|numeric|min:1',
        ]);

        $existe = DB::table('tblcontrat')
            ->where('i_ecole_id', $request->ecole_id)
            ->where('v_annee_scolaire', $request->annee_scolaire)
            ->exists();

        if ($existe) {

            return back()->with(
                'error',
                'Cette école possède déjà un contrat pour cette année scolaire.'
            );
        }

        DB::table('tblcontrat')->insert([

            'v_annee_scolaire' => $request->annee_scolaire,
            'i_etablissement_id' => $request->etablissement_id,
            'i_ecole_id' => $request->ecole_id,

            'd_datedebut' => $request->datedebut,
            'd_datefin' => $request->datefin,

            'd_montant' => $request->montant,

            'i_user_id' => auth()->id(),

            'b_desabled' => 1,

            'd_datecreation' => now()
        ]);

        return back()->with(
            'success',
            'Contrat enregistré avec succès.'
        );
    }
}
