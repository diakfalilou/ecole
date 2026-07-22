<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MenuService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

            /*
            |--------------------------------------------------------------------------
            | MENUS
            |--------------------------------------------------------------------------
            */
            $menusRaw = MenuService::getMenusByUser();

            $menus = [];

            foreach ($menusRaw as $item) {

                $menuId = $item->menu_id;

                // créer menu parent UNE seule fois
                if (!isset($menus[$menuId])) {
                    $menus[$menuId] = [
                        'id' => $menuId,
                        'nom_menu' => $item->nom_menu,
                        'icon_menu' => $item->icon_menu,
                        'lien_menu' => $item->lien_menu,
                        'sousmenus' => []
                    ];
                }

                // ajouter sous-menu sans duplication
                if ($item->sousmenu_id) {
                    $menus[$menuId]['sousmenus'][$item->sousmenu_id] = [
                        'id' => $item->sousmenu_id,
                        'nom' => $item->nom_sousmenu,
                        'route' => $item->route_sousmenu,
                        'icon' => $item->icon_sousmenu,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ECOLE
            |--------------------------------------------------------------------------
            */
            $ecole = null;

            $slug = Request::route('slug');

            if ($slug) {
                $ecole = DB::table('tbecole')
                    ->where('v_slugecole', $slug)
                    ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | SLUG
            |--------------------------------------------------------------------------
            */
            $slug = Request::route('slug');

            /*
            |--------------------------------------------------------------------------
            | ECOLE
            |--------------------------------------------------------------------------
            */
            $ecole = null;

            if ($slug) {
                $ecole = DB::table('tbecole')
                    ->where('v_slugecole', $slug)
                    ->first();
            }

            /*







            /*
            |--------------------------------------------------------------------------
            | PARTAGE DES DONNÉES
            |--------------------------------------------------------------------------
            */
            $view->with([
                'menus' => $menus,
                'ecole' => $ecole,
            ]);
        });








    }
}
