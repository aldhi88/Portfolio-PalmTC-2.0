<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(){
        $data['title'] = "Summary Report";
        $data['desc'] = "Display all summary report data.";
        return view('modules.report.index', compact('data'));
    }
}
