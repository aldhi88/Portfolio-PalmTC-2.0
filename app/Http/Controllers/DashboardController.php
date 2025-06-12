<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $data['title'] = "Dashboard Summary";
        $data['desc'] = "Display all summary data in statistical format";
        return view('modules.dashboard.index', compact('data'));
    }
}
