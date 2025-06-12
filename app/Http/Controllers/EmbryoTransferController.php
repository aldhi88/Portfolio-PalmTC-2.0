<?php

namespace App\Http\Controllers;

use App\Models\TcBottle;
use App\Models\TcBottleInitDetail;
use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoList;
use App\Models\TcEmbryoOb;
use App\Models\TcEmbryoObDetail;
use App\Models\TcEmbryoTransfer;
use App\Models\TcEmbryoTransferBottle;
use App\Models\TcEmbryoTransferBottleWork;
use App\Models\TcEmbryoTransferStock;
use App\Models\TcGerminBottle;
use App\Models\TcGerminList;
use App\Models\TcInit;
use App\Models\TcLaminar;
use App\Models\TcLiquidBottle;
use App\Models\TcLiquidList;
use App\Models\TcLiquidOb;
use App\Models\TcMediumStock;
use App\Models\TcSolidList;
use App\Models\TcSuspenList;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EmbryoTransferController extends Controller
    {
    public function index()
    {
        $data['title'] = "Embryogenesis Transfer (View Per Sample)";
        $data['desc'] = "Display all available sample to transfer";
        return view('modules.embryo_transfer.index', compact('data'));
    }
    public function dtIndex(Request $request)
    {
        $data = TcInit::select('tc_inits.*')
            ->whereHas('tc_embryo_obs',function(Builder $query){
                $query->where('status','!=',0);
            })
            ->withCount('tc_embryo_transfers as transfer_count')
            ->withCount([
                'tc_embryo_transfer_bottles as sum_embryo' => function($q){
                    $q->select(DB::raw('SUM(bottle_embryo)'));
                }
            ])
            ->withCount([
                'tc_embryo_transfer_bottles as has_transfer' => function($q){
                    $q->select(DB::raw('SUM(bottle_embryo) - SUM(bottle_left)'));
                }
            ])
            ->withCount([
                'tc_embryo_transfer_bottles as not_transfer' => function($q){
                    $q->select(DB::raw('SUM(bottle_left)'));
                }
            ])
            ->withCount([
                'tc_embryo_obs as obs_count' => function($q){
                    $q->where('status',1);
                }
            ])
            ->with([
                'tc_samples:id,sample_number,program',
            ])
        ;
        return DataTables::of($data)
            ->editColumn('sample_number_format', function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('embryo-transfers.show',$data->id)."'>View</a>
                ";
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function create($id)
    {
        session()->forget([
            'embtrans_step1',
            'embtrans_step2',
            'embtrans_step3',
            'embtrans_step4',
        ]);
        $data['title'] = "Embryogenesis Transfer";
        $data['desc'] = "Create new transfer bottle.";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        return view('modules.embryo_transfer.create', compact('data'));
    }
    //step1
    public function getStep1(Request $request)
    {
        $page = 'step1_create';
        $data['worker'] = TcWorker::select('id','code')->where('status',1)->get();
        $data['laminar'] = TcLaminar::select('id','code')->get();
        $q = TcEmbryoTransferBottle::select('tc_embryo_bottle_id')
            ->where('bottle_left','>',0)->get();
        foreach ($q as $key => $value) {
            $aryBottleId[] = $value->tc_embryo_bottle_id;
        }
        $data['subCultere'] = TcEmbryoBottle::select('sub')
            ->groupBy('sub')
            ->orderBy('sub','asc')
            ->where('tc_init_id',$request->initId)
            ->whereIn('id',$aryBottleId)
            ->get();

        if(session()->has("embtrans_step1")){
            $dtSession = session('embtrans_step1')['data'];
            $page = session('embtrans_step1')['page'];
            $dtSession['workerCode'] = TcWorker::where('id',$dtSession['tc_worker_id'])
                ->first()->getAttribute('code');
            $dtSession['laminarCode'] = TcLaminar::where('id',$dtSession['tc_laminar_id'])
                ->first()->getAttribute('code');
            $dtSession['transferDate'] = Carbon::parse($dtSession['transfer_date'])->format('d/m/Y');

            if(!is_null($request->page)){
                $page = $request->page;
            }
            return view('modules.embryo_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.embryo_transfer.component.'.$page,compact('data'));
    }
    public function finishStep1(Request $request)
    {
        $dtSession['data'] = $request->except('_token');
        $dtSession['page'] = 'step1_read';
        $dtStep2['page'] = 'step2_create';
        $dtStep2['data'] = [];
        session([
            'embtrans_step1' => $dtSession,
            'embtrans_step2' => $dtStep2
        ]);
    }
    //step2
    public function getStep2(Request $request)
    {
        $page = 'step2_blank';
        $data['initId'] = $request->initId;

        $qCode = DB::raw('convert(varchar,tc_embryo_bottles.bottle_date, 103) as bottle_date_format');
        if(config('database.default') != 'sqlsrv'){
            $qCode = DB::raw('DATE_FORMAT(tc_embryo_bottles.bottle_date, "%d/%m/%Y") as bottle_date_format');
        }

        if(session()->has("embtrans_step2")){
            $data['bottles'] = TcEmbryoTransferBottle::select([
                'tc_embryo_transfer_bottles.*',
                $qCode
            ])
                ->leftJoin('tc_embryo_bottles','tc_embryo_bottles.id','=','tc_embryo_transfer_bottles.tc_embryo_bottle_id')
                ->where('tc_embryo_transfer_bottles.tc_init_id', $request->initId)
                ->where('bottle_left','>',0)
                ->whereHas('tc_embryo_bottles',function($q){
                    $q->where('sub',session('embtrans_step1')['data']['sub']);
                })
                ->get();
            $dtSession = session('embtrans_step2')['data'];
            $page = session('embtrans_step2')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }
            $data['total'] = (collect($dtSession)->sum('work_bottle'));
            return view('modules.embryo_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.embryo_transfer.component.'.$page,compact('data'));
    }
    public function addItemStep2(Request $request)
    {
        $dt = $request->except('_token');
        $dtSession = session('embtrans_step2');
        $dtSession['page'] = 'step2_create';
        $dtSessionData = collect($dtSession['data']);
        $obsId = $request->id;
        $dtFilter = array_values($dtSessionData->where('id',$obsId)->toArray());
        if(count($dtFilter) != 0){
            $oldCount = $dtFilter[0]['work_bottle'];
            $newCount = $request->work_bottle;
            $dt['work_bottle'] = $oldCount + $newCount;
            $dtSession['data'] = $dtSessionData->filter(function($value,$key) use($obsId){
                return $value['id'] != $obsId;
            })->toArray();
        }
        array_push($dtSession['data'],$dt);
        session(['embtrans_step2' => $dtSession]);
    }
    public function delItemStep2(Request $request)
    {
        $obsId = $request->id;
        $dtSession = session('embtrans_step2');
        $dtCollect = collect($dtSession['data']);
        $dtSession['data'] = $dtCollect->filter(function($value,$key) use($obsId){
            return $value['id'] != $obsId;
        })->toArray();
        session(['embtrans_step2' => $dtSession]);
    }
    public function finishStep2(Request $request)
    {
        $dtSess = session('embtrans_step2');
        if(count($dtSess['data']) == 0){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area-step2',
                    'msg' => 'Error, no data observation.',
                ],
            ]);
        }else{
            $dtSess['page'] = 'step2_read';
            $dtStep3['data'] = [];
            $dtStep3['page'] = 'step3_create';
            $dtStep3['medStock']['callus'] = [];
            $dtStep3['medStock']['solid'] = [];
            $dtStep3['medStock']['suspen'] = [];
            session([
                'embtrans_step2' => $dtSess,
                'embtrans_step3' => $dtStep3,
            ]);
        }
    }
    // step3
    public function getStep3(Request $request)
    {
        $page = 'step3_blank';
        $data['initId'] = $request->initId;
        if(session()->has("embtrans_step3")){
            $dtSession = session('embtrans_step3')['data'];
            $page = session('embtrans_step3')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }
            return view('modules.embryo_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.embryo_transfer.component.'.$page,compact('data'));
    }
    public function getMedStock(Request $request)
    {
        session(['modalMediumStock' => $request->for]);
        $qCollect = collect(
            TcMediumStock::where('stock','>',0)
                ->with([
                    'tc_mediums:id,code',
                    'tc_bottles:id,code',
                    'tc_agars:id,code',
                ])
                ->get()->toArray()
        );
        $data['medStock'] = $qCollect->filter(function($value,$key){
            return $value['current_stock'] != 0;
        })->toArray();

        $data['medStockCallus'] = session('embtrans_step3')['medStock']['callus'];
        $data['medStockSolid'] = session('embtrans_step3')['medStock']['solid'];
        $data['medStockSuspen'] = session('embtrans_step3')['medStock']['suspen'];
        $data['medStockPicked'] = session('embtrans_step3')['medStock'][$request->for];
        $data['for'] = $request->for;
        if($request->for != 'callus'){

            if($request->for == 'suspen'){
                $aryBottleInit = ['liquid_column1','liquid_column2'];
            }else{
                $aryBottleInit = ['germin_column1','germin_column2'];
            }
            $q = TcBottleInitDetail::select('tc_bottle_id')
                ->whereHas('tc_bottle_inits',function(Builder $q) use($aryBottleInit){
                    $q->whereIn('keyword',$aryBottleInit);
                })->get()->toArray();
            $data['allowBottle'] = array_column($q,'tc_bottle_id'); //note!
        }else{
            $data['allowBottle'] = array_column(TcBottle::select('id')->get()->toArray(),'id');
        }
        return view('modules.embryo_transfer.component.medium_stock',compact('data'));
    }
    public function addStock(Request $request)
    {
        $newData = $request->except('_token');
        $id = $newData['id'];
        $dtSess = session('embtrans_step3');
        $dtMedStock = collect($dtSess['medStock'][$request->for]);
        $dtMedStockFilter = array_values($dtMedStock->where('id',$id)->toArray());
        if(count($dtMedStockFilter) !=0 ){
            $oldCount = $dtMedStockFilter[0]['used_stock'];
            $newCount = $newData['used_stock'];
            $newData['used_stock'] = $oldCount + $newCount;
            $dtSess['medStock'][$request->for] = $dtMedStock->filter(function($value,$key) use($id){
                return $value['id'] != $id;
            })->toArray();
        }
        array_push($dtSess['medStock'][$request->for],$newData);
        session(['embtrans_step3' => $dtSess]);
        return response()->json(['for' => $request->for]);
    }
    public function delStock(Request $request)
    {
        $id = $request->id;
        $dtSession = session('embtrans_step3');
        $dtCollect = collect($dtSession['medStock'][$request->for]);
        $dtSession['medStock'][$request->for] = $dtCollect->filter(function($value,$key) use($id){
            return $value['id'] != $id;
        })->toArray();
        session(['embtrans_step3' => $dtSession]);
        return response()->json(['for' => $request->for]);
    }
    public function finishAddStock(Request $request)
    {
        $data = collect(session('embtrans_step3')['medStock']);
        $sum = $data->sum('used_stock');
        return response()->json([
            'status' => 'success',
            'data' => [
                'sum' => $sum,
            ],
        ]);
    }
    public function closeModalStock(Request $request)
    {
        $section = session('modalMediumStock');
        $data = collect(session('embtrans_step3')['medStock'][$section]);
        $total = $data->sum('used_stock');
        return response()->json([
            'for' => $section,
            'total' => $total
        ]);
    }
    public function finishStep3(Request $request)
    {
        $callus = $request->to_callus;
        $solid = $request->to_solid;
        $suspen = $request->to_suspen;
        if(
            $callus == 0 &&
            $solid == 0 &&
            $suspen == 0
        ){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-step3',
                    'msg' => 'Error, no data new bottle transfer.',
                ],
            ]);
        }
        $dtSession = session('embtrans_step3');
        $dtSession['data'] = $request->except('_token');
        $dtSession['page'] = 'step3_read';
        $dtNextStep['page'] = 'step4_create';
        $dtNextStep['data'] = [];
        session([
            'embtrans_step3' => $dtSession,
            'embtrans_step4' => $dtNextStep
        ]);
    }
    //step4
    public function getStep4(Request $request)
    {
        $page = 'step4_blank';
        $data['initId'] = $request->initId;
        if(session()->has("embtrans_step4")){
            $dtSession = session('embtrans_step4')['data'];
            if(count($dtSession)==0){
                $dtSession = session('embtrans_step2')['data'];
            }
            $page = session('embtrans_step4')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }

            return view('modules.embryo_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.embryo_transfer.component.'.$page,compact('data'));
    }
    public function finishStep4(Request $request)
    {
        $dtSession = session('embtrans_step4');
        $dtSession['data'] = session('embtrans_step2')['data'];
        foreach ($dtSession['data'] as $key => $value) {
            $val = $request->input('input_'.$value['id']);
            $dtSession['data'][$key]['back_bottle'] = $val;
        }
        $dtSession['page'] = 'step4_read';
        session([
            'embtrans_step4' => $dtSession,
        ]);
    }
    public function finishTransfer(Request $request)
    {
        $return = true;
        if(
            session()->has('embtrans_step1') &&
            session()->has('embtrans_step2') &&
            session()->has('embtrans_step3') &&
            session()->has('embtrans_step4')
        ){
            if(
                session('embtrans_step1')['page'] &&
                session('embtrans_step2')['page'] &&
                session('embtrans_step3')['page'] &&
                session('embtrans_step4')['data']
            ){
                $dtStep1 = collect(session('embtrans_step1')['data']);
                $dtStep2 = collect(session('embtrans_step2')['data']);
                $dtStep3 = collect(session('embtrans_step3')['medStock']);
                $dtStep4 = collect(session('embtrans_step4')['data']);

                try {
                    // insert ke table tc_embryo_transfers
                    $dt1 = $dtStep1->toArray();
                    $dt1['tc_init_id'] = $request->tc_init_id;
                    $dt1['to_callus'] = (collect($dtStep3['callus']))->sum('used_stock');
                    $dt1['to_solid'] = (collect($dtStep3['solid']))->sum('used_stock');
                    $dt1['to_suspen'] = (collect($dtStep3['suspen']))->sum('used_stock');
                    $q = TcEmbryoTransfer::create($dt1);
                    $transferId = $q->id;

                    // insert ke table tc_embryo_transfer_bottle_works dan tc_embryo_lists
                    $dtList['tc_init_id'] =$request->tc_init_id;
                    $dtList['tc_worker_id'] = $dtStep1['tc_worker_id'];
                    $dtList['tc_embryo_transfer_id'] = $transferId;
                    foreach ($dtStep4 as $key => $value) {
                        $bottleId = TcEmbryoTransferBottle::where('id',$value['id'])->first()->getAttribute('tc_embryo_bottle_id');
                        $dtList['tc_embryo_bottle_id'] = $bottleId;
                        $dtList['first_total'] = TcEmbryoList::where('tc_embryo_bottle_id',$bottleId)
                            ->orderBy('last_total','asc')->first()->getAttribute('last_total');
                        $dtList['last_total'] = $dtList['first_total'] - ($value['work_bottle'] - $value['back_bottle']);
                        TcEmbryoList::storeList($dtList,'in');
                        $dt2[] = [
                            'tc_init_id' => $request->tc_init_id,
                            'tc_embryo_transfer_id' => $transferId,
                            'tc_embryo_transfer_bottle_id' => $value['id'],
                            'first_total' => $value['first_total'],
                            'total_work' => $value['work_bottle'],
                            'back_bottle' => $value['back_bottle'],
                        ];
                        TcEmbryoTransferBottle::where('id',$value['id'])
                            ->decrement('bottle_left',($value['work_bottle']));
                    }
                    TcEmbryoTransferBottleWork::insert($dt2);

                    // insert ke table tc_embryo_transfer_bottle_stocks
                    if(count($dtStep3['callus']) != 0){
                        foreach ($dtStep3['callus'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_embryo_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 1
                            ];
                        }
                        TcEmbryoTransferStock::insert($dt3);unset($dt3);

                        // insert ke table tc_embryo_bottles
                        $dt4['tc_init_id'] = $request->tc_init_id;
                        $dt4['tc_embryo_transfer_id'] = $transferId;
                        $dt4['tc_worker_id'] = $dt1['tc_worker_id'];
                        $dt4['tc_laminar_id'] = $dt1['tc_laminar_id'];
                        $dt4['sub'] = $dt1['sub'] + 1;
                        $dt4['number_of_bottle'] = $dt1['to_callus'];
                        $dt4['status'] = 1;
                        $dt4['desc'] = $dt1['comment'];
                        $dt4['bottle_date'] = $dt1['transfer_date'];
                        TcEmbryoBottle::create($dt4);
                    }
                    if(count($dtStep3['solid']) != 0){
                        $q = TcGerminBottle::whereNotNull('tc_embryo_transfer_id')
                            ->where('tc_init_id',$request->tc_init_id)->orderBy('alpha','desc')->get();
                        $nextAlpha = count($q)==0?'A':getAlpha($q->first()->alpha);
                        foreach ($dtStep3['solid'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_embryo_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 2,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                            $medBottleId = TcMediumStock::where('id',$value['id'])->first()->getAttribute('tc_bottle_id');
                            $q2 = TcLiquidBottle::where('bottle_date',$dt1['transfer_date'])->where('tc_init_id',$request->tc_init_id)->get();
                            if(count($q2)!=0){
                                $nextAlpha = $q2->first()->alpha;
                            }
                            $dt5[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_embryo_transfer_id' => $transferId,
                                'tc_worker_id' => $dt1['tc_worker_id'],
                                'tc_laminar_id' => $dt1['tc_laminar_id'],
                                'tc_bottle_id' => $medBottleId,
                                'sub' => $dt1['sub'],
                                'type' => 'Direct',
                                'alpha' => $nextAlpha,
                                'bottle_count' => $value['used_stock'],
                                'bottle_date' => $dt1['transfer_date'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcEmbryoTransferStock::insert($dt3);unset($dt3);
                        TcGerminBottle::insert($dt5);unset($dt5);
                    }
                    if(count($dtStep3['suspen']) != 0){
                        $q = TcLiquidBottle::whereNotNull('tc_embryo_transfer_id')
                            ->where('tc_init_id',$request->tc_init_id)->orderBy('alpha','desc')->get();
                        $nextAlpha = count($q)==0?'A':getAlpha($q->first()->alpha);
                        foreach ($dtStep3['suspen'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_embryo_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 3,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                            $medBottleId = TcMediumStock::where('id',$value['id'])->first()->getAttribute('tc_bottle_id');
                            $q2 = TcLiquidBottle::where('bottle_date',$dt1['transfer_date'])->where('tc_init_id',$request->tc_init_id)->get();
                            if(count($q2)!=0){
                                $nextAlpha = $q2->first()->alpha;
                            }
                            $dt6[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_embryo_transfer_id' => $transferId,
                                'sub' => $dt1['sub'],
                                'alpha' => $nextAlpha,
                                'cycle' => 0,
                                'tc_worker_id' => $dtStep1['tc_worker_id'],
                                'tc_laminar_id' => $dtStep1['tc_laminar_id'],
                                'tc_bottle_id' => $medBottleId,
                                'bottle_count' => $value['used_stock'],
                                'bottle_date' => $dt1['transfer_date'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcEmbryoTransferStock::insert($dt3);unset($dt3);
                        TcLiquidBottle::insert($dt6);unset($dt6);
                    }

                }catch(Throwable $e) {report($e);}
                $return = 1;
            }else{
                $return = 0;
            }
        }else{
            $return = 0;
        }

        if($return == 0){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-finish',
                    'msg' => 'Error, please complate all step before finish.',
                ],
            ]);
        }else{
            session()->forget([
                'embtrans_step1',
                'embtrans_step2',
                'embtrans_step3',
                'embtrans_step4',
            ]);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'redirect' => route('embryo-transfers.show',$request->tc_init_id),
                ],
            ]);
        }


    }

    public function show($id)
    {
        $data['title'] = "Embryogenesis Transfer (View Detail)";
        $data['desc'] = "Display all detail transfer";
        $q = TcInit::select('tc_inits.*')
        ->where('id',$id)
        ->whereHas('tc_embryo_obs')
        ->withCount('tc_embryo_transfers as transfer_count')
        ->withCount([
            'tc_embryo_transfer_bottles as sum_embryo' => function($q){
                $q->select(DB::raw('SUM(bottle_embryo)'));
            }
        ])
        ->withCount([
            'tc_embryo_transfer_bottles as has_transfer' => function($q){
                $q->select(DB::raw('SUM(bottle_embryo) - SUM(bottle_left)'));
            }
        ])
        ->withCount([
            'tc_embryo_transfer_bottles as not_transfer' => function($q){
                $q->select(DB::raw('SUM(bottle_left)'));
            }
        ])
        ->with([
            'tc_samples:id,sample_number,program'
        ])
        ->first();

        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['transferCount'] = $q->transfer_count;
        $data['sumEmbryo'] = $q->sum_embryo;
        $data['hasTransfer'] = $q->has_transfer;
        $data['notTransfer'] = $q->not_transfer;
        $data['initId'] = $id;
        $q = TcEmbryoTransferBottle::where('tc_init_id',$id)->where('bottle_left','>',0)->get()->count();
        $data['allowTransfer'] = $q!=0?true:false;
        return view('modules.embryo_transfer.show', compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcEmbryoTransferBottle::select([
                'tc_embryo_transfer_bottles.*',
                // DB::raw('(bottle_embryo-bottle_left) as transferred')
            ])
            ->where('tc_embryo_transfer_bottles.tc_init_id',$request->initId)
            // ->where('bottle_left','!=',0)
            ->with([
                'tc_embryo_obs',
                'tc_embryo_bottles',
                'tc_embryo_transfer_bottle_works'
            ])
            ->withCount(['tc_embryo_transfer_bottle_works as sum_total_work' => function($q){
                $q->select(DB::raw('sum(total_work)'));
            }])
            ->withCount(['tc_embryo_transfer_bottle_works as sum_bottle_back' => function($q){
                $q->select(DB::raw('sum(back_bottle)'));
            }])
            ->withCount(['tc_embryo_transfer_bottle_works as transferred' => function($q){
                $q->select(DB::raw('sum(total_work) - sum(back_bottle)'));
            }])
        ;
        return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data->tc_embryo_bottles->bottle_date)->format('d/m/Y');
            })
            ->addColumn('work_date_format',function($data){
                return Carbon::parse($data->tc_embryo_obs->work_date)->format('d/m/Y');
            })
            ->editColumn('sum_total_work',function($data){
                return $data->sum_total_work==null?0:$data->sum_total_work;
            })
            ->editColumn('transferred',function($data){
                return $data->transferred==null?0:$data->transferred;
            })
            ->editColumn('sum_bottle_back',function($data){
                return $data->sum_bottle_back==null?0:$data->sum_bottle_back;
            })
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $data = TcEmbryoTransfer::select([
                'tc_embryo_transfers.*',
            ])
            ->where('tc_embryo_transfers.tc_init_id',$request->initId)
            ->with([
                'tc_workers:id,code',
                'tc_laminars:id,code',
            ])
        ;
        // dd($data->get()->toArray());
         return DataTables::of($data)
            ->addColumn('transfer_date_format',function($data){
                return Carbon::parse($data->transfer_date)->format('d/m/Y');
            })
            ->addColumn('action',function($data){
                $isAllow = TcEmbryoTransfer::delAllow($data);
                $delEl = null;
                if($isAllow){
                    $delEl = '
                        <button type="button" class="btn btn-danger py-0" data-toggle="modal" data-target="#delModal" data-id="'.$data->id.'">Delete</button>
                    ';
                }
                $el = '
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-primary py-0 printByTransfer" transfer-id="'.$data->id.'">Print</button>
                        '.$delEl.'
                    </div>
                ';
                return $el;
            })
            ->smart(false)
            ->toJson();
    }

    public function destroy($id)
    {
        $q = TcEmbryoTransferBottleWork::select('tc_embryo_transfer_bottle_id','total_work','back_bottle')
            ->where('tc_embryo_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            TcEmbryoTransferBottle::where('id',$value->tc_embryo_transfer_bottle_id)
                ->increment('bottle_left',$value->total_work);
        }
        TcEmbryoTransferBottleWork::where('tc_embryo_transfer_id',$id)->forceDelete();
        TcEmbryoTransfer::where('id',$id)->forceDelete();

        $q = TcEmbryoList::select('tc_embryo_bottle_id')->where('tc_embryo_transfer_id',$id)->get()->toArray();
        $bottleId = array_column($q,'tc_embryo_bottle_id');
        TcEmbryoBottle::whereIn('id',$bottleId)->update(['status' => 1]);

        TcEmbryoList::where('tc_embryo_transfer_id',$id)->forceDelete();
        TcEmbryoTransferStock::where('tc_embryo_transfer_id',$id)->forceDelete();
        TcEmbryoBottle::where('tc_embryo_transfer_id',$id)->forceDelete();
        TcLiquidBottle::where('tc_embryo_transfer_id',$id)->forceDelete();
        TcGerminBottle::where('tc_embryo_transfer_id',$id)->forceDelete();
        return alert(1,'Success, has been deleted.','alert-area-1');
    }
    public function parsingDataDel($id)
    {
        $q = TcEmbryoTransfer::select('id','transfer_date')
            ->where('id',$id)
            ->first();
        return response()->json([
            'attr' => Carbon::parse($q->transfer_date)->format('d/m/Y'),
            'id' => $q->id,
        ]);
    }

    public function printBlankForm(Request $request){
        $data['title'] = "Print Blank Embryo Transfer Form";
        $data['desc'] = "Printing transfer form before input transfer result";
        if($request->page == 1){
            $data['totalRow'] = $request->page * 25;
        }else{
            $data['totalRow'] = ($request->page * 27) - 2;
        }
        return view('modules.embryo_transfer.print.form_blank',compact('data'));
    }
    public function printBlankForm2(Request $request){
        $data['title'] = "Print Blank Embryo Transfer Form";
        $data['desc'] = "Printing transfer form before input transfer result";
        if($request->page == 1){
            $data['totalRow'] = $request->page * 25;
        }else{
            $data['totalRow'] = ($request->page * 27) - 2;
        }
        return view('modules.embryo_transfer.print.form_blank2',compact('data'));
    }

    // print label
    public function printByTransfer(Request $request){
        $data['title'] = "Print Label Embryo Transfer";
        $data['desc'] = "Print Label Transfer Date";
        $q = TcEmbryoTransfer::where('id',$request->id)->first();
        $q2 = TcEmbryoTransferStock::where('tc_embryo_transfer_id',$request->id)->get();
        dd($q2->toArray());
        $index = 0;
        foreach ($q2 as $key => $value) {
            $loop = $value->used_stock;
            for ($i=0; $i < $loop ; $i++) {
                $data['transfer'][$index]['sample_number'] = $q->tc_inits->tc_samples->sample_number_display;
                $data['transfer'][$index]['sub'] = $q->sub;

                $data['transfer'][$index]['medium_code'] = $value->tc_medium_stocks->tc_mediums->code;
                $data['transfer'][$index]['worker_code'] = $q->tc_workers->code;
                $data['transfer'][$index]['type'] = $value->type;
                $data['transfer'][$index]['transfer_date'] = Carbon::parse($q->transfer_date)->format('d.m.y');
                if($value->type == 2){
                    $data['transfer'][$index]['alpha'] = "B";
                    $data['transfer'][$index]['cat'] = "S";
                }
                if($value->type == 3){
                    $data['transfer'][$index]['alpha'] = "B";
                    $data['transfer'][$index]['cat'] = "D";
                }

                $index++;
            }
        }
        // dd($data['transfer']);
        return view('modules.embryo_transfer.print_label_layout',compact('data'));
    }

}
