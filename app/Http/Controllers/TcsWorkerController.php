<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TcsWorkerController extends Controller
{
    public function data()
    {
        $data['title'] = "Worker Data";
        $data['desc'] = "Display all available Worker data";
        $data['lw'] = 'worker.worker-data';
        return view('livewire.worker.index',compact('data'));
    }
}
