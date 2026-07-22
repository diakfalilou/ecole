<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexEcoleController extends Controller
{
    /**
     * Afficher la liste des écoles
     */
    public function index()
    {

        $ecoles = DB::table('tbecole')
            ->leftJoin(
                'tbletablissement',
                'tbecole.i_idetablissement',
                '=',
                'tbletablissement.i_idetablissement'
            )
            ->select(
                'tbecole.*',
                'tbletablissement.v_nometablissement'
            )
            ->orderBy('tbecole.i_idecole', 'desc')
            ->get();

        return view('admin.ecole.index', compact('ecoles'));

    }
}
