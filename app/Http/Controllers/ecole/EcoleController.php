<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EcoleController extends Controller
{
    public function login($slug)
    {

        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->firstOrFail();

        return view('ecoles.auth.login', compact('ecole'));
    }

    public function doLogin(Request $request, $slug)
    {
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->firstOrFail();

        $credentials = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|min:4',
            ],
            [
                'email.required' => 'L’adresse email est obligatoire.',
                'email.email' => 'Veuillez entrer une adresse email valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit contenir au moins 4 caractères.',
            ]
        );

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();
            $anneescolaire = DB::table('tblcontrat')
            ->where('i_ecole_id', $ecole->i_idecole)
            ->orderBy('i_contrat_id', 'desc')
            ->value('v_annee_scolaire');

            if ($user->del_user == 1) {

                Auth::logout();

                return back()->withErrors([
                    'user' => 'Votre compte est momentanément désactivé.'
                ]);
            }

            /**
             * Vérification que l'utilisateur appartient à cette école
             */
            // dd($user->ecole_id.' '.$ecole->i_idecole);
            if ($user->ecole_id != $ecole->i_idecole) {

                Auth::logout();

                return back()->withErrors([
                    'user' => 'Vous n’êtes pas autorisé à accéder à cette école.'
                ]);
            }
            if (!$anneescolaire) {

                Auth::logout();

                return   back()->withErrors([
                    'user' => 'Accès refusé : aucun contrat actif pour cette école.'
                ]);
            }
            session([
                'nomuser'      => $user->name,
                'user_id'      => $user->id,
                'ecole_id'     => $ecole->i_idecole,
                'nom_ecole'    => $ecole->v_nomecole,
                'slug_ecole'   => $ecole->v_slugecole,
                'anneescolaire'=> $anneescolaire
            ]);

            return redirect()
                ->route('ecole.dashboard', $slug)
                ->with('success', 'Bienvenue ' . $user->name);
        }

        return back()
            ->with('error', 'Adresse email ou mot de passe incorrect.')
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $slug = session('slug_ecole'); // 👈 important

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($slug) {
            return redirect()->route('ecole.login', $slug);
        }

        // fallback si session perdue
        return redirect('/');
    }
}
