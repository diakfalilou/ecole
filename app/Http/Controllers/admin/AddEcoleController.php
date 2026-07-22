<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddEcoleController extends Controller
{
    /**
     * Afficher le formulaire d'ajout
     */
    public function add_ecole()
    {

        $etablissements = DB::table('tbletablissement')
            ->where('bt_etat_etablissement', 1)
            ->orderBy('v_nometablissement', 'asc')
            ->get();

        return view('admin.ecole.add', compact('etablissements'));

    }

    /**
     * Enregistrer une école
     */
    public function store(Request $request)
    {

        $validated = $request->validate(

            [

                'i_idetablissement' => 'required|integer',

                'v_nomecole' => 'required|string|max:255|unique:tbecole,v_nomecole',

                'v_codeecole' => 'nullable|string|max:100',

                't_adresseecole' => 'nullable|string',

                'v_telephone1ecole' => 'nullable|string|max:22',

                'v_telephone2ecole' => 'nullable|string|max:22',

                'v_adressemailv_telephone1ecole' => 'nullable|email|max:222',

                'v_nomdirecteurecole' => 'nullable|string|max:255',

                'i_userID' => 'required|integer',

                'bt_etat_ecole' => 'required|integer',

                // LOGO
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            ],

            [

                'i_idetablissement.required' => 'Veuillez sélectionner un établissement.',

                'v_nomecole.required' => 'Le nom de l’école est obligatoire.',

                'v_nomecole.unique' => 'Cette école existe déjà.',

                'logo.image' => 'Le fichier doit être une image.',

                'logo.mimes' => 'Le logo doit être au format jpg, jpeg, png ou webp.',

                'logo.max' => 'Le logo ne doit pas dépasser 5 MB.',

            ]

        );

        try {
            // INSERTION
            $id_ecole = DB::table('tbecole')->insertGetId([
                'i_idetablissement' => $validated['i_idetablissement'],
                'v_nomecole' => $validated['v_nomecole'],
                'v_codeecole' => $validated['v_codeecole'] ?? null,
                't_adresseecole' => $validated['t_adresseecole'] ?? null,
                'v_telephone1ecole' => $validated['v_telephone1ecole'] ?? null,
                'v_telephone2ecole' => $validated['v_telephone2ecole'] ?? null,
                'v_adressemailv_telephone1ecole' => $validated['v_adressemailv_telephone1ecole'] ?? null,
                'v_nomdirecteurecole' => $validated['v_nomdirecteurecole'] ?? null,
                'i_userID' => $validated['i_userID'],
                'bt_etat_ecole' => $validated['bt_etat_ecole'],
                'v_slugecole' => Str::slug($validated['v_nomecole']),
            ]);
            // UPLOAD LOGO
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $file = $request->file('logo');
                $filename = 'logo_ecole_' . $id_ecole . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('logo_ecole');
                if (!file_exists($destinationPath)) {mkdir($destinationPath, 0755, true);}
                $file->move($destinationPath, $filename);
                DB::table('tbecole')->where('i_idecole', $id_ecole)->update(['logo' => 'logo_ecole/' . $filename]);
            }
            return redirect()->route('add.ecole')->with('success', 'École ajoutée avec succès');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([

                    'error' => $e->getMessage()

                ]);
        }
    }
}
