<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class AddUsersController extends Controller
{
    //
    public function add_users(){
        $data_etablissement=DB::table('tbletablissement')->get();
        return view('admin.users.add',compact('data_etablissement'));
    }
    public function getEcoles($id){
        $ecoles = DB::table('tbecole')
            ->where('i_idetablissement', $id)
            ->where('bt_etat_ecole', 1)
            ->get();
        return response()->json($ecoles);
    }

    public function store(Request $request){

        // dd($request);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable',
            'role' => 'required',
            'password' => 'nullable',
        ]);

        // PASSWORD par défaut si vide
        $password = $request->password ?? '123456';

        $userId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'roles' => $request->role,
            'password' => Hash::make($password),
            'etablissement_id' => $request->etablissement_id ?? 0,
            'ecole_id' => $request->ecole_id ?? 0,
            'del_user' => 0,
            'dateCreation' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =========================
        // UPLOAD IMAGE USER
        // =========================
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {

            $file = $request->file('logo');

            $filename = 'user_' . $userId . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('img_users');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'logo' => 'img_users/' . $filename
                ]);
        }

        return redirect()->back()->with('success', 'Utilisateur créé avec succès');
    }


}
