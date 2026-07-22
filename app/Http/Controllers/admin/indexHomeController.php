<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class indexHomeController extends Controller
{
    //
    public function index_home(){

        return View('admin.home.index');
    }
}
