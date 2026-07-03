<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionHelper
{
    /**
     * Vérifie si l'utilisateur a accès à une route
     */
    public static function hasRoute($route)
    {
        $userId = Auth::id();

        if (!$userId || empty($route)) {
            return false;
        }

        return DB::table('tbluser_menus as um')
            ->join('tblsousmenus as sm', 'sm.id', '=', 'um.sousmenu_id')
            ->where('um.user_id', $userId)
            ->where('sm.route_sousmenu', $route)
            ->where('sm.statut', 1)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur possède au moins une route parmi celles fournies
     *
     * Exemple :
     * PermissionHelper::hasAnyRoute([
     *     'students.show',
     *     'students.edit',
     *     'students.delete'
     * ]);
     */
    public static function hasAnyRoute(array $routes): bool
    {
        foreach ($routes as $route) {
            if (self::hasRoute($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur a accès à un menu principal
     */
    public static function hasMenu($menuId)
    {
        $userId = Auth::id();

        if (!$userId) {
            return false;
        }

        return DB::table('tbluser_menus')
            ->where('user_id', $userId)
            ->where('menu_id', $menuId)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a accès à un sous-menu précis
     */
    public static function hasSubMenu($subMenuId)
    {
        $userId = Auth::id();

        if (!$userId) {
            return false;
        }

        return DB::table('tbluser_menus')
            ->where('user_id', $userId)
            ->where('sousmenu_id', $subMenuId)
            ->exists();
    }
}
