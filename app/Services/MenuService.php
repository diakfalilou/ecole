<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    public static function getMenusByUser()
    {
        $userId = Auth::id();
        return DB::table('tbluser_menus as um')
            ->join('tblmenus as m', 'm.id', '=', 'um.menu_id')
            ->leftJoin('tblsousmenus as sm', 'sm.id', '=', 'um.sousmenu_id')
            ->select(
                'm.id as menu_id',
                'm.nom_menu',
                'm.icon_menu',
                'm.lien_menu',

                'sm.id as sousmenu_id',
                'sm.nom_sousmenu',
                'sm.route_sousmenu',
                'sm.icon_sousmenu'
            )
            ->where('um.user_id', $userId)
            ->where('m.statut', 1)
            ->where('sm.niveau', 0)
            ->where(function ($query) {
                $query->whereNull('sm.id')
                      ->orWhere('sm.statut', 1);
            })
            ->orderBy('m.ordre_menu')
            ->orderBy('sm.ordre_sousmenu')
            ->get();
    }
}
