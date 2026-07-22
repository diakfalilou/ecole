<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddEtablissementController extends Controller
{
    /**
     * Afficher le formulaire
     */
    public function add_etablissement()
    {
        return view('admin.etablissement.add');
    }

    /**
     * Traiter le formulaire
     */
   public function store(Request $request)
{
    $validated = $request->validate(
        [
            'v_nometablissement' => 'required|string|max:255|unique:tbletablissement,v_nometablissement',
            't_adresseetablissement' => 'nullable|string',
            'v_telephone1etablissement' => 'nullable|string|max:22',
            'v_telephone2etablissement' => 'nullable|string|max:22',
            'v_adressemailv_telephone1etablissement' => 'nullable|email|max:222',
            'v_nomfondateurv_telephone1etablissement' => 'nullable|string|max:255',
            'i_userID' => 'required|integer',
            'bt_etat_etablissement' => 'required|integer',
            // LOGO
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ],
        [
            'v_nometablissement.required' => 'Le nom de l’établissement est obligatoire.',
            'v_nometablissement.unique' => 'Ce nom d’établissement existe déjà.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'Le logo doit être au format jpg, jpeg, png ou webp.',
            'logo.max' => 'Le logo ne doit pas dépasser 5 MB.',
        ]
    );

    try {

        $id_etablissement = DB::table('tbletablissement')->insertGetId([

            'v_nometablissement' => $validated['v_nometablissement'],

            't_adresseetablissement' => $validated['t_adresseetablissement'] ?? null,

            'v_telephone1etablissement' => $validated['v_telephone1etablissement'] ?? null,

            'v_telephone2etablissement' => $validated['v_telephone2etablissement'] ?? null,

            'v_adressemailv_telephone1etablissement' => $validated['v_adressemailv_telephone1etablissement'] ?? null,

            'v_nomfondateurv_telephone1etablissement' => $validated['v_nomfondateurv_telephone1etablissement'] ?? null,

            'i_userID' => $validated['i_userID'],

            'bt_etat_etablissement' => $validated['bt_etat_etablissement'],
        ]);

        // Upload logo
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {

            $file = $request->file('logo');

            $filename = 'logo_' . $id_etablissement . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('logo_etablissement');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            DB::table('tbletablissement')
                ->where('i_idetablissement', $id_etablissement)
                ->update([
                    'logo' => 'logo_etablissement/' . $filename
                ]);
        }

        return redirect()
            ->route('add.etablissement')
            ->with('success', 'Établissement ajouté avec succès');

    } catch (\Exception $e) {

        return back()
            ->withInput()
            ->withErrors([
                'error' => $e->getMessage()
            ]);
    }
}
}
