<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexUsersController extends Controller
{
    //
    public function index_users(){
        $users = DB::table('users')
        ->leftJoin('tbletablissement', 'tbletablissement.i_idetablissement', '=', 'users.etablissement_id')
        ->leftJoin('tbecole', 'tbecole.i_idecole', '=', 'users.ecole_id')
        ->select(
            'users.*',
            'tbletablissement.v_nometablissement',
            'tbecole.v_nomecole'
        )
        ->get();
        return view('admin.users.index',compact('users'));
    }
}
