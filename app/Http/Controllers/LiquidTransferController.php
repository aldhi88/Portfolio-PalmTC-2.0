<?php

namespace App\Http\Controllers;

use App\Models\TcBottleInitDetail;
use App\Models\TcInit;
use App\Models\TcLaminar;
use App\Models\TcLiquidBottle;
use App\Models\TcLiquidTransaction;
use App\Models\TcLiquidTransfer;
use App\Models\TcLiquidTransferBottle;
use App\Models\TcLiquidTransferBottleWork;
use App\Models\TcLiquidTransferStock;
use App\Models\TcMaturBottle;
use App\Models\TcMediumStock;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Database\Eloquent\Builder;

class LiquidTransferController extends Controller
{
    public function index()
    {
        $data['title'] = "Liquid Transfer (View Per Sample)";
        $data['desc'] = "Display all available sample to transfer";
        return view('modules.liquid_transfer.index', compact('data'));
    }
    public function dtIndex(Request $request)
    {
        $data = TcInit::select('tc_inits.*')
            ->whereHas('tc_liquid_obs',function(Builder $query){
                $query->where('status','!=',0);
            })
            ->withCount('tc_liquid_transfers as transfer_count')
            ->withCount([
                'tc_liquid_transfer_bottles as sum_liquid' => function($q){
                    $q->select(DB::raw('SUM(bottle_liquid)'));
                }
            ])
            ->withCount([
                'tc_liquid_transfer_bottles as has_transfer' => function($q){
                    $q->select(DB::raw('SUM(bottle_liquid) - SUM(bottle_left)'));
                }
            ])
            ->withCount([
                'tc_liquid_transfer_bottles as not_transfer' => function($q){
                    $q->select(DB::raw('SUM(bottle_left)'));
                }
            ])
            ->withCount([
                'tc_liquid_obs as obs_count' => function($q){
                    $q->where('status',1);
                }
            ])
            ->with([
                'tc_samples:id,sample_number,program'
            ])
        ;
        return DataTables::of($data)
            ->editColumn('sample_number_format', function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('liquid-transfers.show',$data->id)."'>View</a>
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
            'liqtrans_step1',
            'liqtrans_step2',
            'liqtrans_step3',
            'liqtrans_step4',
        ]);
        $data['title'] = "Liquid Transfer";
        $data['desc'] = "Create new transfer bottle.";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        return view('modules.liquid_transfer.create', compact('data'));
    }
    //step1
    public function getStep1(Request $request)
    {
        $page = 'step1_create';
        $data['worker'] = TcWorker::select('id','code')->where('status',1)->get();
        $data['laminar'] = TcLaminar::select('id','code')->get();
        $q = TcLiquidTransferBottle::select('tc_liquid_bottle_id')
            ->where('bottle_left','>',0)->get();
        foreach ($q as $key => $value) {
            $aryBottleId[] = $value->tc_liquid_bottle_id;
        }
        $data['subCultere'] = TcLiquidBottle::select('alpha','cycle')
            ->groupBy('alpha','cycle')
            ->orderBy('cycle','asc')
            ->where('tc_init_id',$request->initId)
            ->whereIn('id',$aryBottleId)
            ->get();

        if(session()->has("liqtrans_step1")){
            $dtSession = session('liqtrans_step1')['data'];
            $page = session('liqtrans_step1')['page'];
            $dtSession['workerCode'] = TcWorker::where('id',$dtSession['tc_worker_id'])
                ->first()->getAttribute('code');
            $dtSession['laminarCode'] = TcLaminar::where('id',$dtSession['tc_laminar_id'])
                ->first()->getAttribute('code');
            $dtSession['transferDate'] = Carbon::parse($dtSession['transfer_date'])->format('d/m/Y');

            if(!is_null($request->page)){
                $page = $request->page;
            }
            return view('modules.liquid_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.liquid_transfer.component.'.$page,compact('data'));
    }
    public function finishStep1(Request $request)
    {
        $dtSession['data'] = $request->except('_token');
        $dtSession['data']['alpha'] = (explode('/',$request->alphaCycle))[0];
        $dtSession['data']['cycle'] = (explode('/',$request->alphaCycle))[1];
        $dtSession['page'] = 'step1_read';
        $dtStep2['page'] = 'step2_create';
        $dtStep2['data'] = [];
        session([
            'liqtrans_step1' => $dtSession,
            'liqtrans_step2' => $dtStep2
        ]);
    }
    //step2
    public function getStep2(Request $request)
    {
        $page = 'step2_blank';
        $data['initId'] = $request->initId;

        $qCode = DB::raw('convert(varchar,tc_liquid_bottles.bottle_date, 103) as bottle_date_format');
        if(config('database.default') != 'sqlsrv'){
            $qCode = DB::raw('DATE_FORMAT(tc_liquid_bottles.bottle_date, "%d/%m/%Y") as bottle_date_format');
        }

        if(session()->has("liqtrans_step2")){
            $data['bottles'] = TcLiquidTransferBottle::select([
                'tc_liquid_transfer_bottles.*',
                $qCode
            ])
                ->leftJoin('tc_liquid_bottles','tc_liquid_bottles.id','=','tc_liquid_transfer_bottles.tc_liquid_bottle_id')
                ->where('tc_liquid_transfer_bottles.tc_init_id', $request->initId)
                ->where('bottle_left','>',0)
                ->whereHas('tc_liquid_bottles',function($q){
                    $q->where('alpha',session('liqtrans_step1')['data']['alpha']);
                })
                ->get();
            $dtSession = session('liqtrans_step2')['data'];
            $page = session('liqtrans_step2')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }
            $data['total'] = (collect($dtSession)->sum('work_bottle'));
            return view('modules.liquid_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.liquid_transfer.component.'.$page,compact('data'));
    }
    public function addItemStep2(Request $request)
    {
        $dt = $request->except('_token');
        $dtSession = session('liqtrans_step2');
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
        session(['liqtrans_step2' => $dtSession]);
    }
    public function delItemStep2(Request $request)
    {
        $obsId = $request->id;
        $dtSession = session('liqtrans_step2');
        $dtCollect = collect($dtSession['data']);
        $dtSession['data'] = $dtCollect->filter(function($value,$key) use($obsId){
            return $value['id'] != $obsId;
        })->toArray();
        session(['liqtrans_step2' => $dtSession]);
    }
    public function finishStep2(Request $request)
    {
        $dtSess = session('liqtrans_step2');
        if(count($dtSess['data']) == 0){
            return alert(0,'Error, no data observation.','alert-area-step2');
        }else{
            $dtSess['page'] = 'step2_read';
            $dtStep3['data'] = [];
            $dtStep3['page'] = 'step3_create';
            $dtStep3['medStock']['back'] = [];
            $dtStep3['medStock']['next'] = [];
            session([
                'liqtrans_step2' => $dtSess,
                'liqtrans_step3' => $dtStep3,
            ]);
        }
    }
    // step3
    public function getStep3(Request $request)
    {
        $page = 'step3_blank';
        $data['initId'] = $request->initId;
        if(session()->has("liqtrans_step3")){
            $dtSession = session('liqtrans_step3')['data'];
            $page = session('liqtrans_step3')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }
            return view('modules.liquid_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.liquid_transfer.component.'.$page,compact('data'));
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

        $data['medStockBack'] = session('liqtrans_step3')['medStock']['back'];
        $data['medStockNext'] = session('liqtrans_step3')['medStock']['next'];
        $data['medStockPicked'] = session('liqtrans_step3')['medStock'][$request->for];
        $data['for'] = $request->for;
        if($request->for == 'back'){
            $aryBottleInit = ['liquid_column1','liquid_column2'];
        }else{
            $aryBottleInit = ['matur_column1','matur_column2'];
        }
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q) use($aryBottleInit){
                $q->whereIn('keyword',$aryBottleInit);
            })->get()->toArray();
        $data['allowBottle'] = array_column($q,'tc_bottle_id'); //note!
        return view('modules.liquid_transfer.component.medium_stock',compact('data'));
    }
    public function addStock(Request $request)
    {
        $newData = $request->except('_token');
        $id = $newData['id'];
        $dtSess = session('liqtrans_step3');
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
        session(['liqtrans_step3' => $dtSess]);
        return response()->json(['for' => $request->for]);
    }
    public function delStock(Request $request)
    {
        $id = $request->id;
        $dtSession = session('liqtrans_step3');
        $dtCollect = collect($dtSession['medStock'][$request->for]);
        $dtSession['medStock'][$request->for] = $dtCollect->filter(function($value,$key) use($id){
            return $value['id'] != $id;
        })->toArray();
        session(['liqtrans_step3' => $dtSession]);
        return response()->json(['for' => $request->for]);
    }
    public function finishAddStock(Request $request)
    {
        $data = collect(session('liqtrans_step3')['medStock']);
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
        $data = collect(session('liqtrans_step3')['medStock'][$section]);
        $total = $data->sum('used_stock');
        return response()->json([
            'for' => $section,
            'total' => $total
        ]);
    }
    public function finishStep3(Request $request)
    {
        $back = $request->to_back;
        $next = $request->to_next;
        if(
            $back == 0 &&
            $next == 0
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
        $dtSession = session('liqtrans_step3');
        $dtSession['data'] = $request->except('_token');
        $dtSession['page'] = 'step3_read';
        $dtNextStep['page'] = 'step4_create';
        $dtNextStep['data'] = [];
        session([
            'liqtrans_step3' => $dtSession,
            'liqtrans_step4' => $dtNextStep
        ]);
    }
    //step4
    public function getStep4(Request $request)
    {
        $page = 'step4_blank';
        $data['initId'] = $request->initId;
        if(session()->has("liqtrans_step4")){
            $dtSession = session('liqtrans_step4')['data'];
            if(count($dtSession)==0){
                $dtSession = session('liqtrans_step2')['data'];
            }
            $page = session('liqtrans_step4')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }

            return view('modules.liquid_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.liquid_transfer.component.'.$page,compact('data'));
    }
    public function finishStep4(Request $request)
    {
        $dtSession = session('liqtrans_step4');
        $dtSession['data'] = session('liqtrans_step2')['data'];
        foreach ($dtSession['data'] as $key => $value) {
            $val = $request->input('input_'.$value['id']);
            $dtSession['data'][$key]['back_bottle'] = $val;
        }
        $dtSession['page'] = 'step4_read';
        session([
            'liqtrans_step4' => $dtSession,
        ]);
    }
    public function finishTransfer(Request $request)
    {
        $return = true;
        if(
            session()->has('liqtrans_step1') &&
            session()->has('liqtrans_step2') &&
            session()->has('liqtrans_step3') &&
            session()->has('liqtrans_step4')
        ){
            if(
                session('liqtrans_step1')['page'] &&
                session('liqtrans_step2')['page'] &&
                session('liqtrans_step3')['page'] &&
                session('liqtrans_step4')['data']
            ){
                $dtStep1 = collect(session('liqtrans_step1')['data']);
                $dtStep2 = collect(session('liqtrans_step2')['data']);
                $dtStep3 = collect(session('liqtrans_step3')['medStock']);
                $dtStep4 = collect(session('liqtrans_step4')['data']);
                try {
                    // insert ke table tc_liquid_transfers
                    $dt1 = $dtStep1->toArray();
                    unset($dt1['alphaCycle']);
                    $dt1['tc_init_id'] = $request->tc_init_id;
                    $dt1['to_self'] = (collect($dtStep3['back']))->sum('used_stock');
                    $dt1['to_matur'] = (collect($dtStep3['next']))->sum('used_stock');
                    $q = TcLiquidTransfer::create($dt1);
                    $transferId = $q->id;

                    // insert ke table tc_liquid_transfer_bottle_work dan tc_liquid_lists

                    $dtList['tc_init_id'] =$request->tc_init_id;
                    $dtList['tc_worker_id'] = $dtStep1['tc_worker_id'];
                    $dtList['tc_liquid_transfer_id'] = $transferId;
                    foreach ($dtStep4 as $key => $value) {
                        $bottleId = TcLiquidTransferBottle::where('id',$value['id'])
                            ->first()->getAttribute('tc_liquid_bottle_id');
                        $dtList['tc_liquid_bottle_id'] = $bottleId;
                        $dtList['first_total'] = TcLiquidTransaction::where('tc_liquid_bottle_id',$bottleId)
                            ->orderBy('last_total','asc')->first()->getAttribute('last_total');
                        $dtList['last_total'] = $dtList['first_total'] - ($value['work_bottle'] - $value['back_bottle']);
                        TcLiquidTransaction::storeList($dtList,'in');

                        $dt2[] = [
                            'tc_init_id' => $request->tc_init_id,
                            'tc_liquid_transfer_id' => $transferId,
                            'tc_liquid_transfer_bottle_id' => $value['id'],
                            'first_total' => $value['first_total'],
                            'total_work' => $value['work_bottle'],
                            'back_bottle' => $value['back_bottle'],
                        ];
                        TcLiquidTransferBottle::where('id',$value['id'])->decrement('bottle_left',($value['work_bottle']));
                    }
                    TcLiquidTransferBottleWork::insert($dt2);

                    // insert ke table tc_liquid_transfer_bottle_stocks
                    if(count($dtStep3['back']) != 0){
                        foreach ($dtStep3['back'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_liquid_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                            $medBottleId = TcMediumStock::where('id',$value['id'])->first()->getAttribute('tc_bottle_id');
                            $dt4[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_liquid_transfer_id' => $transferId,
                                'tc_worker_id' => $dt1['tc_worker_id'],
                                'tc_laminar_id' => $dt1['tc_laminar_id'],
                                'tc_bottle_id' => $medBottleId,
                                'sub' => $dtStep2[0]['sub'],
                                'alpha' => $dt1['alpha'],
                                'cycle' => $dt1['cycle'] + 1,
                                'bottle_count' => $value['used_stock'],
                                'bottle_date' => $dt1['transfer_date'],
                                'desc' => $dt1['comment'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcLiquidTransferStock::insert($dt3);unset($dt3);
                        TcLiquidBottle::insert($dt4);unset($dt4);
                    }
                    if(count($dtStep3['next']) != 0){
                        foreach ($dtStep3['next'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_liquid_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 2,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                            $medBottleId = TcMediumStock::where('id',$value['id'])->first()->getAttribute('tc_bottle_id');
                            $dt[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_liquid_transfer_id' => $transferId,
                                'sub' => $dtStep2[0]['sub'],
                                'alpha' => $dt1['alpha'],
                                'tc_worker_id' => $dtStep1['tc_worker_id'],
                                'tc_laminar_id' => $dtStep1['tc_laminar_id'],
                                'tc_bottle_id' => $medBottleId,
                                'bottle_count' => $value['used_stock'],
                                'bottle_date' => $dt1['transfer_date'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcLiquidTransferStock::insert($dt3);unset($dt3);
                        TcMaturBottle::insert($dt);unset($dt);
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
            return alert(0,'Error, please complate all step before finish.','alert-finish');
        }else{
            session()->forget(['liqtrans_step1','liqtrans_step2','liqtrans_step3','liqtrans_step4',]);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'redirect' => route('liquid-transfers.show',$request->tc_init_id),
                ],
            ]);
        }


    }

    public function show($id)
    {
        $q = TcInit::select('tc_inits.*')
        ->where('id',$id)
        ->whereHas('tc_liquid_obs')
        ->withCount('tc_liquid_transfers as transfer_count')
        ->withCount([
            'tc_liquid_transfer_bottles as sum_liquid' => function($q){
                $q->select(DB::raw('SUM(bottle_liquid)'));
            }
        ])
        ->withCount([
            'tc_liquid_transfer_bottles as has_transfer' => function($q){
                $q->select(DB::raw('SUM(bottle_liquid) - SUM(bottle_left)'));
            }
        ])
        ->withCount([
            'tc_liquid_transfer_bottles as not_transfer' => function($q){
                $q->select(DB::raw('SUM(bottle_left)'));
            }
        ])
        ->with([
            'tc_samples:id,sample_number,program'
        ])
        ->first();

        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['transferCount'] = $q->transfer_count;
        $data['sumLiquid'] = $q->sum_liquid;
        $data['hasTransfer'] = $q->has_transfer;
        $data['notTransfer'] = $q->not_transfer;
        $data['initId'] = $id;
        $data['title'] = "Liquid Transfer (View Detail)";
        $data['desc'] = "Display all detail transfer";
        $q = TcLiquidTransferBottle::where('tc_init_id',$id)->where('bottle_left','>',0)->get()->count();
        $data['allowTransfer'] = $q!=0?true:false;
        return view('modules.liquid_transfer.show', compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcLiquidTransferBottle::select([
                'tc_liquid_transfer_bottles.*',
            ])
            ->where('tc_liquid_transfer_bottles.tc_init_id',$request->initId)
            // ->where('bottle_left','!=',0)
            ->with([
                'tc_liquid_obs',
                'tc_liquid_bottles',
                'tc_liquid_transfer_bottle_work'
            ])
            // ->where('bottle_left','>',0)
        ;
         return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data->tc_liquid_bottles->bottle_date)->format('d/m/Y');
            })
            ->addColumn('work_date_format',function($data){
                return Carbon::parse($data->tc_liquid_obs->ob_date)->format('d/m/Y');
            })
            ->addColumn('transferred',function($data){
                return $data->bottle_liquid - $data->bottle_left;
            })
            ->addColumn('bottle_back',function($data){
                return (collect($data->tc_liquid_transfer_bottle_work))->sum('back_bottle');
            })
            ->addColumn('bottle_out',function($data){
                return ($data->bottle_liquid - $data->bottle_left)-(collect($data->tc_liquid_transfer_bottle_work))->sum('back_bottle');
            })
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $data = TcLiquidTransfer::select([
                'tc_liquid_transfers.*',
            ])
            ->where('tc_liquid_transfers.tc_init_id',$request->initId)
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
                $isAllow = TcLiquidTransfer::delAllow($data);
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
        $q = TcLiquidTransferBottleWork::where('tc_liquid_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            $transBottleId = $value->tc_liquid_transfer_bottle_id;
            $bottleWork = $value->total_work;
            $transferBottleWorkId = $value->id;
            TcLiquidTransferBottle::where('id',$transBottleId)
                ->increment('bottle_left',$bottleWork);
            TcLiquidTransferBottleWork::where('id',$transferBottleWorkId)->forceDelete();
        }

        TcLiquidTransfer::where('id',$id)->forceDelete();
        $q = TcLiquidTransaction::select('tc_liquid_bottle_id')->where('tc_liquid_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            $bottleId = $value->tc_liquid_bottle_id;
            TcLiquidBottle::where('id',$bottleId)->update(['status' => 1]);
        }
        TcLiquidTransaction::where('tc_liquid_transfer_id',$id)->forceDelete();
        TcLiquidTransferStock::where('tc_liquid_transfer_id',$id)->forceDelete();
        TcLiquidBottle::where('tc_liquid_transfer_id',$id)->forceDelete();
        TcMaturBottle::where('tc_liquid_transfer_id',$id)->forceDelete();
        return alert(1,'Success, has been deleted.','alert-area-1');
    }
    public function parsingDataDel($id)
    {
        $q = TcLiquidTransfer::select('id','transfer_date')
            ->where('id',$id)
            ->first();
        return response()->json([
            'attr' => Carbon::parse($q->transfer_date)->format('d/m/Y'),
            'id' => $q->id,
        ]);
    }

    // print label
    public function printByTransfer(Request $request){
        $data['title'] = "Print Label Liquid Transfer";
        $data['desc'] = "Print Label Transfer Date";
        $q = TcLiquidTransfer::where('id',$request->id)->first();
        $q2 = TcLiquidTransferStock::where('tc_liquid_transfer_id',$request->id)
            ->get();
        $index = 0;
        foreach ($q2 as $key => $value) {
            $loop = $value->used_stock;
            for ($i=0; $i < $loop ; $i++) {
                $data['transfer'][$index]['sample_number'] = $q->tc_inits->tc_samples->sample_number_display;
                $data['transfer'][$index]['transfer_date'] = Carbon::parse($q->transfer_date)->format('d M Y');
                $data['transfer'][$index]['alpha'] = $q->alpha;
                $data['transfer'][$index]['medium_code'] = $value->tc_medium_stocks->tc_mediums->code;
                $data['transfer'][$index]['worker_code'] = $q->tc_workers->code;
                $data['transfer'][$index]['tahuntanam'] = $q->tc_inits->tc_samples->master_treefile->tahuntanam;
                $data['transfer'][$index]['program'] = $q->tc_inits->tc_samples->program;
                $data['transfer'][$index]['noseleksi'] = $q->tc_inits->tc_samples->master_treefile->noseleksi;

                $index++;
            }
        }
        return view('modules.liquid_transfer.print_label_layout',compact('data'));
    }
}
