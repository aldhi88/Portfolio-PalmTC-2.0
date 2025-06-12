<?php

namespace App\Http\Controllers;

use App\Models\TcInit;
use App\Models\TcInitiation;
use App\Models\TcInitOfBlock;
use App\Models\TcMedium;
use App\Models\TcMediumOpname;
use App\Models\TcMediumStock;
use App\Models\TcObsDetail;
use App\Models\TcSample;
use App\Models\TcWorker;
use App\Models\TcWorkerOfInitiation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TesController extends Controller
{
    public function index(Request $request){
        TcObsDetail::newBottleCallusPerObs(61);
    }
    public function tes(Request $request){
        dd($request->session()->all());
    }

    public function query($table_name){
        if (Str::startsWith($table_name, 'tc_')) {
            DB::table($table_name)
                ->delete();
        } else {
            return "not allow";
        }



    }
}
