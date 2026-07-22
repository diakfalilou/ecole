<?php

namespace App\Http\Controllers\ecole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcoleDashboardController extends Controller
{
    //
    public function index($slug)
    {
        $ecole = DB::table('tbecole')
            ->where('v_slugecole', $slug)
            ->firstOrFail();

        return view('ecoles.dashboard.index', compact('ecole'));
    }
}
