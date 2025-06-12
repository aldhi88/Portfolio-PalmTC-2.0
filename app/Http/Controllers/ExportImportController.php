<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportImportController extends Controller
{
    public function index(){
        $data['title'] = "Export Import";
        $data['desc'] = "Export import data all you need";
        return view('modules.export_import.index', compact('data'));
    }
}
