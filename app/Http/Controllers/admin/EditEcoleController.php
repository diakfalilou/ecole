<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EditEcoleController extends Controller
{
    public function edit_ecole($id_ecole)
    {
       $ecole_edite = DB::table('tbecole')
        ->join('tbletablissement', 'tbletablissement.i_idetablissement', '=', 'tbecole.i_idetablissement')
        ->where('tbecole.i_idecole', $id_ecole)
        ->select(
            'tbecole.*',

            // alias établissement
            'tbletablissement.logo as logo_etablissement',
            'tbletablissement.v_nometablissement',

            // alias école (si tu veux être safe)
            'tbecole.logo as logo_ecole'
        )
        ->first();
        // dd($ecole_edite);
        return view('admin.ecole.edit', compact('ecole_edite'));
    }

    public function update_ecole(Request $request, $id_ecole)
    {
        $validated = $request->validate(

            [
                'v_nomecole' => 'required|string|max:255|unique:tbecole,v_nomecole,' . $id_ecole . ',i_idecole',

                'v_codeecole' => 'nullable|string|max:100',
                't_adresseecole' => 'nullable|string',
                'v_telephone1ecole' => 'nullable|string|max:22',
                'v_telephone2ecole' => 'nullable|string|max:22',
                'v_adressemailv_telephone1ecole' => 'nullable|email|max:222',
                'v_nomdirecteurecole' => 'nullable|string|max:255',

                // LOGO
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ],

            [
                'v_nomecole.required' => 'Le nom de l’école est obligatoire.',
                'v_nomecole.unique' => 'Cette école existe déjà.',

                'logo.image' => 'Le fichier doit être une image.',
                'logo.mimes' => 'Le logo doit être au format jpg, jpeg, png ou webp.',
                'logo.max' => 'Le logo ne doit pas dépasser 5 MB.',
            ]
        );

        try {

            // UPDATE ECOLE (sans champs protégés)
            DB::table('tbecole')
                ->where('i_idecole', $id_ecole)
                ->update([
                    'v_nomecole' => $validated['v_nomecole'],
                    'v_codeecole' => $validated['v_codeecole'] ?? null,
                    't_adresseecole' => $validated['t_adresseecole'] ?? null,
                    'v_telephone1ecole' => $validated['v_telephone1ecole'] ?? null,
                    'v_telephone2ecole' => $validated['v_telephone2ecole'] ?? null,
                    'v_adressemailv_telephone1ecole' => $validated['v_adressemailv_telephone1ecole'] ?? null,
                    'v_nomdirecteurecole' => $validated['v_nomdirecteurecole'] ?? null,
                ]);

            // UPLOAD LOGO (remplacement)
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {

                $file = $request->file('logo');

                $filename = 'logo_ecole_' . $id_ecole . '.' . $file->getClientOriginalExtension();

                $destinationPath = public_path('logo_ecole');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);

                DB::table('tbecole')
                    ->where('i_idecole', $id_ecole)
                    ->update([
                        'logo' => 'logo_ecole/' . $filename
                    ]);
            }
           return redirect()->route('ecoles.edite', ['id_ecole' => $id_ecole])->with('success', 'École modifiée avec succès');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }
}
