<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index(){
        $data['title'] = "Label Printing";
        $data['desc'] = "Printing custom label what you want.";
        return view('modules.label.index', compact('data'));
    }
}
