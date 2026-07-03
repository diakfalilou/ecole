<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexMenuController extends Controller
{
    //
    public function index_menu(){
        $menus1 = DB::table('tblmenus')
            ->whereNull('parent_id')
            ->orderBy('ordre_menu')
            ->get();



        $sousmenus = DB::table('tblsousmenus')
            ->orderBy('ordre_sousmenu')
            ->get();

            //  dd($sousmenus);

        return view('admin.menus.index', compact('menus1', 'sousmenus'));
    }

    public function store_menu(Request $request){
        $request->validate([
            'nom_menu' => 'required|string|max:255',
            'icon_menu' => 'nullable|string|max:100',
            'lien_menu' => 'nullable|string|max:255',
            'ordre_menu' => 'nullable|integer',
            'parent_id' => 'nullable|integer'
        ]);

        DB::table('tblmenus')->insert([
            'nom_menu' => $request->nom_menu,
            'icon_menu' => $request->icon_menu,
            'lien_menu' => $request->lien_menu,
            'ordre_menu' => $request->ordre_menu ?? 0,
            'parent_id' => $request->parent_id, // NULL = menu principal
            'statut' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Menu créé avec succès');
    }

    public function store_sousmenu(Request $request){
        $request->validate([
            'menu_id' => 'required',
            'nom_sousmenu' => 'required',
            'route_sousmenu' => 'nullable',
            'icon_sousmenu' => 'nullable',
            'ordre_sousmenu' => 'nullable'
        ]);

        DB::table('tblsousmenus')->insert([
            'menu_id' => $request->menu_id,
            'nom_sousmenu' => $request->nom_sousmenu,
            'route_sousmenu' => $request->route_sousmenu,
            'icon_sousmenu' => $request->icon_sousmenu,
            'ordre_sousmenu' => $request->ordre_sousmenu ?? 0,
            'statut' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Sous-menu créé avec succès');
    }
}
