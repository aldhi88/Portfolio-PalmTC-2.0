<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(){
        $data['title'] = "Schedule";
        $data['desc'] = "Scheduling data for notification";
        return view('modules.schedule.index', compact('data'));
    }
}
