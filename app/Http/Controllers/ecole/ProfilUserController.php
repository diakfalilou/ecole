<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfilUserController extends Controller
{
   public function profil_user($slug)
    {
        return view('ecoles.users.profil', compact('slug'));
    }

    public function updateProfil(Request $request, $slug)
    {
        $user = Auth::user();

        // =========================
        // VALIDATION
        // =========================

        $request->validate([
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|min:6',
            'confirm_password' => 'nullable|same:new_password'
        ]);

        // =========================
        // UPLOAD PHOTO
        // =========================

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            $file = $request->file('photo');

            $filename = 'user_' . $user->id . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('img_users');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'logo' => 'img_users/' . $filename
                ]);
        }

        // =========================
        // MODIFICATION MOT DE PASSE
        // =========================

        if (
            !empty($request->old_password) ||
            !empty($request->new_password) ||
            !empty($request->confirm_password)
        ) {

            if (!Hash::check($request->old_password, $user->password)) {

                return back()
                    ->withInput()
                    ->with('error', 'Ancien mot de passe incorrect.');
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($request->new_password)
                ]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
