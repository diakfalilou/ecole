<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TableActionService
{
    public static function getActionsByMenu($menuPrincipal)
    {
        return DB::table('tbluser_menus as um')
            ->join('tblmenus as m', 'm.id', '=', 'um.menu_id')
            ->join('tblsousmenus as sm', 'sm.id', '=', 'um.sousmenu_id')
            ->where('um.user_id', Auth::id())
            ->where('m.nom_menu', $menuPrincipal)
            ->where('sm.statut', 1)
            ->where('sm.niveau', 1) // ✅ ICI correction importante
            ->orderBy('sm.ordre_sousmenu')
            ->get([
                'sm.id',
                'sm.nom_sousmenu',
                'sm.route_sousmenu',
                'sm.icon_sousmenu'
            ]);
    }
}
