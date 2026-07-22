<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserMenuController extends Controller
{
    public function index(Request $request)
    {
        $id =$request->id;
        $user = DB::table('users')->where('id', $id)->first();

        $menus1 = DB::table('tblmenus')
            ->whereNull('parent_id')
            ->get();

        $sousmenus = DB::table('tblsousmenus')->get();

        $userMenus = DB::table('tbluser_menus')
            ->where('user_id', $id)
            ->get();

        $menuPermissions = $userMenus
            ->pluck('menu_id')
            ->unique()
            ->toArray();

        $sousmenuPermissions = $userMenus
            ->whereNotNull('sousmenu_id')
            ->pluck('sousmenu_id')
            ->toArray();

            // dd($menus);
        return view(
            'admin.users.menus',
            compact(
                'user',
                'menus1',
                'sousmenus',
                'userMenus',
                'menuPermissions',
                'sousmenuPermissions'
            )
        );
    }

    public function store(Request $request)
    {
        $id =$request->id;
        DB::table('tbluser_menus')
            ->where('user_id', $id)
            ->delete();
        foreach ($request->menus ?? [] as $menu_id) {

            DB::table('tbluser_menus')->insert([
                'user_id' => $id,
                'menu_id' => $menu_id,
                'sousmenu_id' => null,
                'can_view' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($request->sousmenus ?? [] as $sousmenu_id) {

            $sub = DB::table('tblsousmenus')->where('id', $sousmenu_id)->first();

            DB::table('tbluser_menus')->insert([
                'user_id' => $id,
                'menu_id' => $sub->menu_id,
                'sousmenu_id' => $sousmenu_id,
                'can_view' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Menus attribués avec succès');
    }

    public function storeAjax(Request $request)
    {
        try {

            $userId = $request->user_id;
            $menuId = $request->menu_id;
            $sousmenuId = $request->sousmenu_id;
            $checked = filter_var($request->checked, FILTER_VALIDATE_BOOLEAN);

            /*
            |--------------------------------------------------------------------------
            | AJOUT
            |--------------------------------------------------------------------------
            */
            if ($checked) {

                $query = DB::table('tbluser_menus')
                    ->where('user_id', $userId)
                    ->where('menu_id', $menuId);

                if (empty($sousmenuId)) {
                    $query->whereNull('sousmenu_id');
                } else {
                    $query->where('sousmenu_id', $sousmenuId);
                }

                $exists = $query->exists();

                if (!$exists) {

                    DB::table('tbluser_menus')->insert([
                        'user_id'      => $userId,
                        'menu_id'      => $menuId,
                        'sousmenu_id'  => $sousmenuId,
                        'can_view'     => 1,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => empty($sousmenuId)
                        ? 'Menu ajouté avec succès'
                        : 'Sous-menu ajouté avec succès'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | SUPPRESSION
            |--------------------------------------------------------------------------
            */

            // Décoche un menu principal
            if (empty($sousmenuId)) {

                DB::table('tbluser_menus')
                    ->where('user_id', $userId)
                    ->where('menu_id', $menuId)
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Menu et ses sous-menus retirés'
                ]);
            }

            // Décoche un sous-menu uniquement
            DB::table('tbluser_menus')
                ->where('user_id', $userId)
                ->where('menu_id', $menuId)
                ->where('sousmenu_id', $sousmenuId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sous-menu retiré'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
