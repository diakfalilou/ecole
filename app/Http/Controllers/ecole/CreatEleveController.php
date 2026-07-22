<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CreatEleveController extends Controller
{
    public function create($slug){
        abort_unless(PermissionHelper::hasRoute('students.create'),403);
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

        // dd($parent_eleve);
        // dd(DB::table('tblclasse')->get());
        return view('ecoles.eleves.creat',compact('niveaux','slug','parent_eleve'));
    }

    public function searchParent(Request $request){
        $q = trim($request->q);
        if (!$q) {
            return response()->json([]);
        }
        $parents = DB::table('tblparent')
            ->where('i_ecole_id', session('ecole_id'))
            ->where(function ($query) use ($q) {
                $query->where('v_nom_pere', 'like', "%{$q}%")
                    ->orWhere('v_nom_mere', 'like', "%{$q}%")
                    ->orWhere('v_nom_tuteur', 'like', "%{$q}%")
                    ->orWhere('v_telephone_tuteur', 'like', "%{$q}%");
            })
            ->orderBy('d_datecreation', 'desc')
            ->limit(10)
            ->get();

        return response()->json($parents);
    }

    public function getClassesByNiveau(Request $request, $niveau_id)
    {
        $ecole_id = session('ecole_id'); // OK si tu stockes bien l’école ici
        $classes = DB::table('tblclasse')
            ->where('i_niveau_id', $niveau_id)
            ->where('i_ecole_id', $ecole_id)
            ->where('b_desabled', 1)
            ->orderBy('v_nom_classe', 'asc')
        ->get();
        return response()->json($classes);
    }

    public function store_eleve($slug, Request $request){
        if ($request->parent_option == "create") {

            $ecole_id = session('ecole_id');
            $user_id  = Auth::id();

            // Vérification doublon
            $parentExiste = DB::table('tblparent')
            ->where(function ($query) use ($request) {
                $query->where('v_telephone_tuteur', $request->v_telephone_tuteur)
                    ->orWhere('v_email_tuteur', $request->v_email_tuteur);
            })
            ->where('i_ecole_id', session('ecole_id'))
            ->first();
            if ($parentExiste) {
                return back()
                    ->withInput()
                    ->with('error', 'Un parent avec ce numéro de téléphone et cette adresse email existe déjà.');
            }
            // Conversion pour respecter l'ENUM de la table
            $tuteurType = match (strtolower($request->v_tuteur_type)) {
                'père', 'pere' => 'pere',
                'mère', 'mere' => 'mere',
                default => 'autre'
            };
            $id_parent = DB::table('tblparent')->insertGetId([
                'v_nom_pere'            => $request->v_nom_pere,
                'v_telephone_pere'      => $request->v_telephone_pere,
                'v_profession_pere'     => $request->v_profession_pere,
                'v_photo_pere'          => null,

                'v_nom_mere'            => $request->v_nom_mere,
                'v_telephone_mere'      => $request->v_telephone_mere,
                'v_profession_mere'     => $request->v_profession_mere,
                'v_photo_mere'          => null,

                'v_tuteur_type'         => $tuteurType,
                'v_nom_tuteur'          => $request->v_nom_tuteur,
                'v_email_tuteur'        => $request->v_email_tuteur,
                'v_telephone_tuteur'    => $request->v_telephone_tuteur,
                'v_profession_tuteur'   => $request->v_profession_tuteur,
                'v_adresse_tuteur'      => $request->v_adresse_tuteur,
                'v_photo_tuteur'        => null,

                'i_ecole_id'            => $ecole_id,
                'i_user_id'             => $user_id,
                'd_datecreation'        => now(),
                'b_desabled'            => 1,
            ]);
        }else{
            $id_parent=$request->parent_id;
        }

        $id_eleve = DB::table('tbleleve')->insertGetId([
            'v_matricule'      => $request->v_matricule,
            'v_nom'            => $request->v_nom,
            'v_prenom'         => $request->v_prenom,
            'v_genre'          => $request->v_genre,
            'd_date_naissance' => $request->d_date_naissance,
            'v_telephone'      => $request->v_telephone,
            'v_email'          => $request->v_email,
            'v_adresse'       => $request->v_adresse,

            'v_photo'          => null,

            'i_ecole_id'       => session('ecole_id'),
            'i_user_id'        => Auth::id(),
            'i_parenti_id'     => $id_parent,
            'b_desabled'       => 1,
            'd_datecreation'   => now(),
        ]);

        DB::table('tblinscription')->insert([
            'i_eleve_id'        => $id_eleve,
            'i_classe_id'       => $request->classe_id,
            'i_niveau_id'       => $request->niveau_id,
            'i_ecole_id'        => session('ecole_id'),
            'i_user_id'         => Auth::id(),

            'v_annee_scolaire'  => $request->anneescolaire,

            // par défaut
            'v_typeinscription' => 'inscription',

            'd_date_inscription' => now(),
            'b_active'          => 1,
            'b_statut'          => 1,
        ]);

        if ($request->hasFile('v_photo') && $request->file('v_photo')->isValid()) {
            $file = $request->file('v_photo'); // ✅ correction ici
            $filename = 'img_' . $id_eleve . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('img_eleve');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            DB::table('tbleleve')
                ->where('i_eleve_id', $id_eleve)
                ->update([
                    'v_photo' => 'img_eleve/' . $filename
                ]);
        }
        if (
            !empty(trim($request->v_groupe_sanguin)) ||
            !empty(trim($request->v_taille)) ||
            !empty(trim($request->v_poids))
        ) {
            DB::table('tbleleve_medical')->insert([
                'i_eleve_id'        => $id_eleve,
                'i_ecole_id'        => session('ecole_id'),
                'i_user_id'         => Auth::id(),

                'v_groupe_sanguin'  => $request->v_groupe_sanguin,
                'v_taille'          => $request->v_taille,
                'v_poids'           => $request->v_poids,

                'd_datecreation'    => now(),
                'b_desabled'        => 1,
            ]);
        }

        if($request->schoolNamee){
            DB::table('tblencientetablissement')->insert([
                'v_nom_etablissement '=>$request->schoolNameeschoolNamee,
                'v_adresse_eta '=>$request->adresseancienecole,
                'i_eleveId'=>$id_eleve,
                'i_ecole_id'=>session('ecole_id'),
                'i_user_id'=>Auth::id(),
            ]);
        }

        return back()->withInput()->with('success', 'Eleve enregistrez avec succée.');

    }
}
