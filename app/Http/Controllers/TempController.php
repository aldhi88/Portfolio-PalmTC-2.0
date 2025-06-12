<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TempController extends Controller
{
    public function index(){
        $data['title'] = "Temperature";
        $data['desc'] = "Display all temperature data in the room";
        return view('modules.temp.index', compact('data'));
    }
}
