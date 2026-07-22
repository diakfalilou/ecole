<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EditEleveController extends Controller
{
    //
    public function edit_eleve($slug,$id){

        abort_unless(PermissionHelper::hasRoute('edit.eleve'),403);
        $ecole = DB::table('tbecole')
        ->where('v_slugecole', $slug)
        ->first();

        $parent_eleve=DB::table('tblparent')
        ->where('i_ecole_id',session('ecole_id'))
        ->get();

        $niveaux = DB::table('tblniveau')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_niveauID', 'desc')
        ->get();



        $eleve = DB::table('tbleleve')
        ->where('tbleleve.i_eleve_id',$id)
        ->leftJoin('tblparent', 'tbleleve.i_parenti_id', '=', 'tblparent.i_parent_id')
        ->leftJoin('tbleleve_medical', 'tbleleve.i_eleve_id', '=', 'tbleleve_medical.i_eleve_id')
        ->leftJoin('tblancienetablissementeleve', 'tbleleve.i_eleve_id', '=', 'tblancienetablissementeleve.i_eleve_id')
        ->where('tbleleve.i_ecole_id', session('ecole_id'))
        ->select(
            'tbleleve.*',
            'tblparent.v_nom_tuteur as parent_name',
            'tbleleve_medical.v_groupe_sanguin as v_groupe_sanguin',
            'tbleleve_medical.v_taille as v_taille',
            'tbleleve_medical.v_poids as v_poids',
            'tblancienetablissementeleve.v_nom_etablissement as v_nom_etablissement',
            'tblancienetablissementeleve.v_adresse_etablissement as v_adresse_etablissement',
        )
        ->first();
        // dd($eleve);
        return view('ecoles.eleves.edit',compact('slug','parent_eleve','niveaux','eleve','id'));
    }

    public function update_eleve(Request $request, $slug, $id){
        abort_unless(PermissionHelper::hasRoute('edit.eleve'), 403);

        DB::table('tbleleve')
            ->where('i_eleve_id', $id)
            ->update([
                'v_matricule'      => $request->v_matricule,
                'v_nom'            => $request->v_nom,
                'v_prenom'         => $request->v_prenom,
                'v_genre'          => $request->v_genre,
                'd_date_naissance' => $request->d_date_naissance,
                'v_telephone'      => $request->v_telephone,
                'v_email'          => $request->v_email,
                'v_adresse'        => $request->v_adresse,
                't_details_eleve'  => $request->t_details_eleve,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Photo
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('v_photo') && $request->file('v_photo')->isValid()) {

            $file = $request->file('v_photo');

            $filename = 'img_' . $id . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('img_eleve');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            DB::table('tbleleve')
                ->where('i_eleve_id', $id)
                ->update([
                    'v_photo' => 'img_eleve/' . $filename
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Informations médicales
        |--------------------------------------------------------------------------
        */
        $medical = DB::table('tbleleve_medical')
            ->where('i_eleve_id', $id)
            ->first();

        if ($medical) {

            DB::table('tbleleve_medical')
                ->where('i_eleve_id', $id)
                ->update([
                    'v_groupe_sanguin' => $request->v_groupe_sanguin,
                    'v_taille'         => $request->v_taille,
                    'v_poids'          => $request->v_poids,
                ]);

        } else {

            DB::table('tbleleve_medical')->insert([
                'i_eleve_id'        => $id,
                'i_ecole_id'        => session('ecole_id'),
                'i_user_id'         => Auth::id(),
                'v_groupe_sanguin'  => $request->v_groupe_sanguin,
                'v_taille'          => $request->v_taille,
                'v_poids'           => $request->v_poids,
                'd_datecreation'    => now(),
                'b_desabled'        => 1,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ancien établissement
        |--------------------------------------------------------------------------
        */
        $ancien = DB::table('tblancienetablissementeleve')
            ->where('i_eleve_id', $id)
            ->first();

        if ($ancien) {

            DB::table('tblancienetablissementeleve')
                ->where('i_eleve_id', $id)
                ->update([
                    'v_nom_etablissement'     => $request->v_nom_etablissement,
                    'v_adresse_etablissement' => $request->v_adresse_etablissement,
                ]);

        } else {

            DB::table('tblancienetablissementeleve')
                ->insert([
                    'i_eleve_id'              => $id,
                    'i_ecole_id'              => session('ecole_id'),
                    'v_nom_etablissement'     => $request->v_nom_etablissement,
                    'v_adresse_etablissement' => $request->v_adresse_etablissement,
                    'i_user_id'=>Auth::id(),
                ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Élève modifié avec succès.');
    }
}
