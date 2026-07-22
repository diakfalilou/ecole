<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\File;
class AuthController extends Controller
{


    public function login(){

        return view('auth.login')->with('success', 'Bienvenue !');
    }

    public function logoutAllUsers()
    {
        File::cleanDirectory(storage_path('framework/sessions'));
        return back()->with('success', 'Tous les utilisateurs ont été déconnectés.');
    }

    public function dologin(Request $request){

       $credentials = $request->validate(['email' => 'required|email','password' => 'required|min:4',],
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

            // Vérifier si le compte est désactivé
            if (isset($user->del_user) && $user->del_user == 1) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte est momentanément désactivé.'
                ]);
            }
            session([
                'nomuser' => $user->name,
                'user_id' => $user->id,
                'service' => $user->role ?? 'user',
                'langue'  => 'fr',
                'clinique_id' => $user->idclinique,
                'id_service' => $user->service_id,
                'roles' => $user->roles,
            ]);

            if($user->roles=='admin'){
                return redirect()->route('index.home.admin')->with('success', 'Bienvenue !');
            }else{
                 return back()->with('info', 'vous n\'ête pas autoriser a vous connnectez a ce compte.')->withInput($request->only('email'));
            }

        }



        return back()->with('error', 'Adresse email ou mot de passe incorrecte.')->withInput($request->only('email'));
    }


}
