<?php

namespace App\Http\Controllers;

use App\Models\TcAclim;
use App\Models\TcAclimTree;
use App\Models\TcBottleInitDetail;
use App\Models\TcInit;
use App\Models\TcLaminar;
use App\Models\TcMediumStock;
use App\Models\TcRootingBottle;
use App\Models\TcRootingTransaction;
use App\Models\TcRootingTransfer;
use App\Models\TcRootingTransferBottle;
use App\Models\TcRootingTransferBottleWork;
use App\Models\TcRootingTransferStock;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RootingTransferController extends Controller
{
    public function index()
    {
        $data['title'] = "Rooting Transfer (View Per Sample)";
        $data['desc'] = "Display all available sample to transfer";
        return view('modules.rooting_transfer.index', compact('data'));
    }
    public function dtIndex(Request $request)
    {
        $data = TcInit::select('tc_inits.*')
            ->whereHas('tc_rooting_obs',function(Builder $query){
                $query->where('status','!=',0);
            })
            ->withCount('tc_rooting_transfers as transfer_count')
            ->withCount([
                'tc_rooting_transfer_bottles as sum_rooting' => function($q){
                    $q->select(DB::raw('SUM(bottle_rooting)'));
                }
            ])
            ->withCount([
                'tc_rooting_transfer_bottles as has_transfer' => function($q){
                    $q->select(DB::raw('SUM(bottle_rooting) - SUM(bottle_left)'));
                }
            ])
            ->withCount([
                'tc_rooting_transfer_bottles as not_transfer' => function($q){
                    $q->select(DB::raw('SUM(bottle_left)'));
                }
            ])
            ->withCount([
                'tc_rooting_obs as obs_count' => function($q){
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
                        <a class='text-primary' href='".route('rooting-transfers.show',$data->id)."'>View</a>
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
            'rootingtrans_step1',
            'rootingtrans_step2',
            'rootingtrans_step3',
            'rootingtrans_step4',
        ]);
        $data['title'] = "Rooting Transfer";
        $data['desc'] = "Create new transfer bottle.";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        return view('modules.rooting_transfer.create', compact('data'));
    }
    //step1
    public function getStep1(Request $request)
    {
        $page = 'step1_create';
        $data['worker'] = TcWorker::select('id','code')->where('status',1)->get();
        $data['laminar'] = TcLaminar::select('id','code')->get();
        $q = TcRootingTransferBottle::select('tc_rooting_bottle_id')
            ->where('bottle_left','>',0)->get();
        foreach ($q as $key => $value) {
            $aryBottleId[] = $value->tc_rooting_bottle_id;
        }
        $data['subCultere'] = TcRootingBottle::select('alpha')
            ->groupBy('alpha')
            ->orderBy('alpha','asc')
            ->where('tc_init_id',$request->initId)
            ->whereIn('id',$aryBottleId)
            ->get();

        if(session()->has("rootingtrans_step1")){
            $dtSession = session('rootingtrans_step1')['data'];
            $page = session('rootingtrans_step1')['page'];
            $dtSession['workerCode'] = TcWorker::where('id',$dtSession['tc_worker_id'])
                ->first()->getAttribute('code');
            $dtSession['laminarCode'] = TcLaminar::where('id',$dtSession['tc_laminar_id'])
                ->first()->getAttribute('code');
            $dtSession['transferDate'] = Carbon::parse($dtSession['transfer_date'])->format('d/m/Y');

            if(!is_null($request->page)){
                $page = $request->page;
            }
            return view('modules.rooting_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.rooting_transfer.component.'.$page,compact('data'));
    }
    public function finishStep1(Request $request)
    {
        $dtSession['data'] = $request->except('_token');
        $dtSession['page'] = 'step1_read';
        $dtStep2['page'] = 'step2_create';
        $dtStep2['data'] = [];
        session([
            'rootingtrans_step1' => $dtSession,
            'rootingtrans_step2' => $dtStep2
        ]);
    }
    //step2
    public function getStep2(Request $request)
    {
        $page = 'step2_blank';
        $data['initId'] = $request->initId;

        $qCode = DB::raw('convert(varchar,tc_rooting_bottles.bottle_date, 103) as bottle_date_format');
        if(config('database.default') != 'sqlsrv'){
            $qCode = DB::raw('DATE_FORMAT(tc_rooting_bottles.bottle_date, "%d/%m/%Y") as bottle_date_format');
        }

        if(session()->has("rootingtrans_step2")){
            $data['bottles'] = TcRootingTransferBottle::select([
                    'tc_rooting_transfer_bottles.*',
                    $qCode
                ])
                ->leftJoin('tc_rooting_bottles','tc_rooting_bottles.id','=','tc_rooting_transfer_bottles.tc_rooting_bottle_id')
                ->where('tc_rooting_transfer_bottles.tc_init_id', $request->initId)
                ->where('bottle_left','>',0)
                ->whereHas('tc_rooting_bottles',function($q){
                    $q->where('alpha',session('rootingtrans_step1')['data']['alpha']);
                })
                ->get();
            // dd($data['bottles']->toArray());
            $dtSession = session('rootingtrans_step2')['data'];
            $page = session('rootingtrans_step2')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }
            $data['total'] = (collect($dtSession)->sum('work_bottle'));
            $data['totalLeaf'] = (collect($dtSession)->sum('work_leaf'));
            return view('modules.rooting_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.rooting_transfer.component.'.$page,compact('data'));
    }
    public function addItemStep2(Request $request)
    {
        $dt = $request->except('_token');
        $dt['work_bottle'] = (int)round($dt['work_leaf']/2);
        if($request->type == 2){
            $dt['work_bottle'] = $dt['work_leaf'];
        }
        $dtSession = session('rootingtrans_step2');
        $dtSession['page'] = 'step2_create';
        $dtSessionData = collect($dtSession['data']);
        $obsId = $request->id;
        $dtFilter = array_values($dtSessionData->where('id',$obsId)->toArray());
        if(count($dtFilter) != 0){
            $oldCount = $dtFilter[0]['work_leaf'];
            $newCount = $request->work_leaf;
            $dt['work_leaf'] = $oldCount + $newCount;
            $dt['work_bottle'] = (int)round($dt['work_leaf']/2);
            if($request->type == 2){
                $dt['work_bottle'] = $dt['work_leaf'];
            }
            $dtSession['data'] = $dtSessionData->filter(function($value,$key) use($obsId){
                return $value['id'] != $obsId;
            })->toArray();
        }
        array_push($dtSession['data'],$dt);
        session(['rootingtrans_step2' => $dtSession]);
    }
    public function delItemStep2(Request $request)
    {
        $obsId = $request->id;
        $dtSession = session('rootingtrans_step2');
        $dtCollect = collect($dtSession['data']);
        $dtSession['data'] = $dtCollect->filter(function($value,$key) use($obsId){
            return $value['id'] != $obsId;
        })->toArray();
        session(['rootingtrans_step2' => $dtSession]);
    }
    public function finishStep2(Request $request)
    {
        $dtSess = session('rootingtrans_step2');
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
            $dtStep3['medStock']['back'] = [];
            $dtStep3['medStock']['root2'] = [];
            session([
                'rootingtrans_step2' => $dtSess,
                'rootingtrans_step3' => $dtStep3,
            ]);
        }
    }
    // step3
    public function getStep3(Request $request)
    {
        $page = 'step3_blank';
        $data['initId'] = $request->initId;
        if(session()->has("rootingtrans_step2")){
            $dtSession2 = session('rootingtrans_step2')['data'];
            $data['totalLeaf'] = (collect($dtSession2)->sum('work_leaf'));
        }
        if(session()->has("rootingtrans_step3")){
            $dtSession = session('rootingtrans_step3')['data'];
            $page = session('rootingtrans_step3')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }
            return view('modules.rooting_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.rooting_transfer.component.'.$page,compact('data'));
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

        $data['medStockBack'] = session('rootingtrans_step3')['medStock']['back'];
        $data['medStockNext'] = session('rootingtrans_step3')['medStock']['root2'];
        $data['medStockPicked'] = session('rootingtrans_step3')['medStock'][$request->for];
        $data['for'] = $request->for;
        if($request->for == 'back'){
            $aryBottleInit = ['rooting_column1'];
            $q = TcBottleInitDetail::select('tc_bottle_id')
                ->whereHas('tc_bottle_inits',function(Builder $q) use($aryBottleInit){
                    $q->whereIn('keyword',$aryBottleInit);
                })->get()->toArray();
            $data['allowBottle'] = array_column($q,'tc_bottle_id'); //note!
        }else if($request->for == 'root2'){
            $aryBottleInit = ['rooting_column2'];
            $q = TcBottleInitDetail::select('tc_bottle_id')
                ->whereHas('tc_bottle_inits',function(Builder $q) use($aryBottleInit){
                    $q->whereIn('keyword',$aryBottleInit);
                })->get()->toArray();
            $data['allowBottle'] = array_column($q,'tc_bottle_id'); //note!
        }else{
            $data['allowBottle'] = false;
        }

        return view('modules.rooting_transfer.component.medium_stock',compact('data'));
    }
    public function addStock(Request $request)
    {
        $newData = $request->except('_token');
        $id = $newData['id'];
        $dtSess = session('rootingtrans_step3');
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
        session(['rootingtrans_step3' => $dtSess]);
        return response()->json(['for' => $request->for]);
    }
    public function delStock(Request $request)
    {
        $id = $request->id;
        $dtSession = session('rootingtrans_step3');
        $dtCollect = collect($dtSession['medStock'][$request->for]);
        $dtSession['medStock'][$request->for] = $dtCollect->filter(function($value,$key) use($id){
            return $value['id'] != $id;
        })->toArray();
        session(['rootingtrans_step3' => $dtSession]);
        return response()->json(['for' => $request->for]);
    }
    public function finishAddStock(Request $request)
    {
        $data = collect(session('rootingtrans_step3')['medStock']);
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
        $data = collect(session('rootingtrans_step3')['medStock'][$section]);
        $total = $data->sum('used_stock');
        return response()->json([
            'for' => $section,
            'total' => $total,
            'leaf' => $total*2,
        ]);
    }
    public function finishStep3(Request $request)
    {
        dump($request->all());
        $back = $request->to_back;
        $root2 = $request->to_root2;
        $next = $request->to_next;
        if($back == 0 && $root2 == 0 && $next == 0){
            return alert(0,'Error, no data new bottle transfer','alert-step3');
        }
        $dtSession = session('rootingtrans_step3');
        $data = $request->except('_token');
        $data['to_root2_leaf'] = $request->to_root2;
        $dtSession['data'] = $data;
        $dtSession['page'] = 'step3_read';
        $dtNextStep['page'] = 'step4_create';
        $dtNextStep['data'] = [];
        session([
            'rootingtrans_step3' => $dtSession,
            'rootingtrans_step4' => $dtNextStep
        ]);
    }
    //step4
    public function getStep4(Request $request)
    {
        $page = 'step4_blank';
        $data['initId'] = $request->initId;
        if(session()->has("rootingtrans_step4")){
            $dtSession = session('rootingtrans_step4')['data'];
            if(count($dtSession)==0){
                $dtSession = session('rootingtrans_step2')['data'];
            }
            $page = session('rootingtrans_step4')['page'];
            if(!is_null($request->page)){
                $page = $request->page;
            }

            return view('modules.rooting_transfer.component.'.$page,compact('data','dtSession'));
        }

        return view('modules.rooting_transfer.component.'.$page,compact('data'));
    }
    public function finishStep4(Request $request)
    {
        $dtSession = session('rootingtrans_step4');
        $dtSession['data'] = session('rootingtrans_step2')['data'];
        foreach ($dtSession['data'] as $key => $value) {
            $valLeaf = $request->input('input_leaf_'.$value['id']);
            $val = (int)round($valLeaf/2);
            $dtSession['data'][$key]['back_bottle'] = $val;
            $dtSession['data'][$key]['back_leaf'] = $valLeaf;
        }
        $dtSession['page'] = 'step4_read';
        session([
            'rootingtrans_step4' => $dtSession,
        ]);
    }
    public function finishTransfer(Request $request)
    {
        $return = true;
        if(
            session()->has('rootingtrans_step1') &&
            session()->has('rootingtrans_step2') &&
            session()->has('rootingtrans_step3') &&
            session()->has('rootingtrans_step4')
        ){
            if(
                session('rootingtrans_step1')['page'] &&
                session('rootingtrans_step2')['page'] &&
                session('rootingtrans_step3')['page'] &&
                session('rootingtrans_step4')['page']
            ){
                $dtStep1 = collect(session('rootingtrans_step1')['data']);
                $dtStep2 = collect(session('rootingtrans_step2')['data']);
                $dtStep3 = collect(session('rootingtrans_step3')['medStock']);
                $dtStep3Data = collect(session('rootingtrans_step3')['data']);
                $dtStep4 = collect(session('rootingtrans_step4')['data']);
                try {

                    // insert ke table tc_rooting_transfers
                    $dt1 = $dtStep1->toArray();
                    $dt1['tc_init_id'] = $request->tc_init_id;
                    $dt1['to_root1_bottle'] = $dtStep3Data['to_back'];
                    $dt1['to_root1_leaf'] = $dtStep3Data['leaf_count'];
                    $dt1['to_root2'] = $dtStep3Data['to_root2'];
                    $dt1['to_aclim'] = $dtStep3Data['to_next'];
                    $q = TcRootingTransfer::create($dt1);
                    $transferId = $q->id;

                    // insert ke table tc_rooting_transfer_bottle_works dan tc_rooting_lists

                    $dtList['tc_init_id'] =$request->tc_init_id;
                    $dtList['tc_worker_id'] = $dtStep1['tc_worker_id'];
                    $dtList['tc_rooting_transfer_id'] = $transferId;
                    // dd($dtStep4);
                    foreach ($dtStep4 as $key => $value) {
                        $bottleId = TcRootingTransferBottle::where('id',$value['id'])->first()
                            ->getAttribute('tc_rooting_bottle_id');
                        $dtList['tc_rooting_bottle_id'] = $bottleId;
                        $q = TcRootingTransaction::where('tc_rooting_bottle_id',$bottleId)
                            ->orderBy('last_total','asc')->first();
                        $dtList['first_total'] = $q->last_total;
                        $dtList['first_leaf'] = $q->last_leaf;
                        $dtList['last_total'] = $dtList['first_total'] - ($value['work_bottle'] - $value['back_bottle']);
                        $dtList['last_leaf'] = $dtList['first_leaf'] - ($value['work_leaf'] - $value['back_leaf']);
                        // dump($dtList);
                        TcRootingTransaction::storeList($dtList,'in');

                        $dt2[] = [
                            'tc_init_id' => $request->tc_init_id,
                            'tc_rooting_transfer_id' => $transferId,
                            'tc_rooting_transfer_bottle_id' => $value['id'],
                            'first_total' => $value['first_total'],
                            'first_leaf' => $value['first_leaf'],
                            'total_work' => $value['work_bottle'],
                            'leaf_work' => $value['work_leaf'],
                            'back_bottle' => $value['back_bottle'],
                            'back_leaf' => $value['back_leaf'],
                        ];
                        TcRootingTransferBottle::where('id',$value['id'])->update([
                            'bottle_left' => DB::raw('bottle_left - '.($value['work_bottle']-$value['back_bottle'])),
                            'leaf_left' => DB::raw('leaf_left - '.($value['work_leaf']-$value['back_leaf'])),
                        ]);
                    }
                    // dd('stop');
                    TcRootingTransferBottleWork::insert($dt2);

                    // insert ke table tc_rooting_transfer_bottle_stocks
                    if(count($dtStep3['back']) != 0){
                        foreach ($dtStep3['back'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_rooting_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 1
                            ];
                            $medBottleId = TcMediumStock::where('id',$value['id'])->first()->getAttribute('tc_bottle_id');
                            $dt[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_rooting_transfer_id' => $transferId,
                                'tc_worker_id' => $dt1['tc_worker_id'],
                                'tc_laminar_id' => $dt1['tc_laminar_id'],
                                'tc_bottle_id' => $medBottleId,
                                'sub' => $dtStep2[0]['sub'],
                                'bottle_type' => $dtStep2[0]['bottle_type'],
                                'type' => 1,
                                'alpha' => $dt1['alpha'],
                                'bottle_count' => $value['used_stock'],
                                'leaf_count' => $dtStep3Data['leaf_count'],
                                'bottle_date' => $dt1['transfer_date'],
                                'desc' => $dt1['comment'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcRootingTransferStock::insert($dt3);unset($dt3);
                        TcRootingBottle::insert($dt);unset($dt);
                    }

                    if(count($dtStep3['root2']) != 0){
                        foreach ($dtStep3['root2'] as $key => $value) {
                            $dt3[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_rooting_transfer_id' => $transferId,
                                'tc_medium_stock_id' => $value['id'],
                                'used_stock' => $value['used_stock'],
                                'type' => 1
                            ];
                            $medBottleId = TcMediumStock::where('id',$value['id'])->first()->getAttribute('tc_bottle_id');
                            $dt[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_rooting_transfer_id' => $transferId,
                                'tc_worker_id' => $dt1['tc_worker_id'],
                                'tc_laminar_id' => $dt1['tc_laminar_id'],
                                'tc_bottle_id' => $medBottleId,
                                'sub' => $dtStep2[0]['sub'],
                                'bottle_type' => $dtStep2[0]['bottle_type'],
                                'type' => 2,
                                'alpha' => $dt1['alpha'],
                                'bottle_count' => $value['used_stock'],
                                'leaf_count' => $dtStep3Data['to_root2'],
                                'bottle_date' => $dt1['transfer_date'],
                                'desc' => $dt1['comment'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcRootingTransferStock::insert($dt3);unset($dt3);
                        TcRootingBottle::insert($dt);unset($dt);
                    }

                    if($dtStep3Data['to_next'] != 0){
                        $toNext = $dtStep3Data['to_next'];
                        $dt = [
                            'tc_init_id' => $request->tc_init_id,
                            'tc_rooting_transfer_id' => $transferId,
                            'tc_worker_id' => $dt1['tc_worker_id'],
                            'sub' => $dtStep2[0]['sub'],
                            'type' =>$dtStep2[0]['bottle_type'],
                            'alpha' => $dt1['alpha'],
                            'tree_date' => $dt1['transfer_date'],
                        ];

                        $q = TcAclim::create($dt);
                        $aclimId = $q->id;

                        for ($i=1; $i <= $toNext ; $i++) {
                            $dtUse[] = [
                                'tc_init_id' => $request->tc_init_id,
                                'tc_aclim_id' => $aclimId,
                                'index_number' => $i,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                        TcAclimTree::insert($dtUse);
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
            session()->forget([
                'rootingtrans_step1','rootingtrans_step2','rootingtrans_step3','rootingtrans_step4']);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'redirect' => route('rooting-transfers.show',$request->tc_init_id),
                ],
            ]);
        }


    }

    public function show($id)
    {
        $q = TcInit::select('tc_inits.*')
        ->where('id',$id)
        ->whereHas('tc_rooting_obs')
        ->withCount('tc_rooting_transfers as transfer_count')
        ->withCount([
            'tc_rooting_transfer_bottles as sum_rooting' => function($q){
                $q->select(DB::raw('SUM(bottle_rooting)'));
            }
        ])
        ->withCount([
            'tc_rooting_transfer_bottles as has_transfer' => function($q){
                $q->select(DB::raw('SUM(bottle_rooting) - SUM(bottle_left)'));
            }
        ])
        ->withCount([
            'tc_rooting_transfer_bottles as not_transfer' => function($q){
                $q->select(DB::raw('SUM(bottle_left)'));
            }
        ])
        ->with([
            'tc_samples:id,sample_number,program'
        ])
        ->first();

        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['transferCount'] = $q->transfer_count;
        $data['sumRooting'] = $q->sum_rooting;
        $data['hasTransfer'] = $q->has_transfer;
        $data['notTransfer'] = $q->not_transfer;
        $data['initId'] = $id;
        $data['title'] = "Rooting Transfer (View Detail)";
        $data['desc'] = "Display all detail transfer";
        $q = TcRootingTransferBottle::where('tc_init_id',$id)->where('bottle_left','>',0)->get()->count();
        $data['allowTransfer'] = $q!=0?true:false;
        return view('modules.rooting_transfer.show', compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcRootingTransferBottle::select([
                'tc_rooting_transfer_bottles.*',
            ])
            ->where('tc_rooting_transfer_bottles.tc_init_id',$request->initId)
            ->with([
                'tc_rooting_obs',
                'tc_rooting_bottles',
                'tc_rooting_transfer_bottle_works'
            ])
            // ->where('bottle_left','>',0)
        ;
        // dd($a);
         return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data->tc_rooting_bottles->bottle_date)->format('d/m/Y');
            })
            ->addColumn('work_date_format',function($data){
                return Carbon::parse($data->tc_rooting_obs->ob_date)->format('d/m/Y');
            })
            ->addColumn('bottle_transferred',function($data){
                // dd($data->tc_rooting_transfer_bottle_works);
                return (collect($data->tc_rooting_transfer_bottle_works))->sum('total_work');
                // return $data->bottle_rooting - $data->bottle_left;
            })
            ->addColumn('leaf_transferred',function($data){
                // dd($data->tc_rooting_transfer_bottle_works);
                return (collect($data->tc_rooting_transfer_bottle_works))->sum('leaf_work');
                // return $data->bottle_rooting - $data->bottle_left;
            })
            ->addColumn('bottle_back',function($data){
                return (collect($data->tc_rooting_transfer_bottle_works))->sum('back_bottle');
            })
            ->addColumn('leaf_back',function($data){
                return (collect($data->tc_rooting_transfer_bottle_works))->sum('back_leaf');
            })
            ->addColumn('bottle_out',function($data){
                return (collect($data->tc_rooting_transfer_bottle_works))->sum('total_work') -
                    (collect($data->tc_rooting_transfer_bottle_works))->sum('back_bottle');
                // return ($data->bottle_rooting - $data->bottle_left)-(collect($data->tc_rooting_transfer_bottle_works))->sum('back_bottle');
            })
            ->addColumn('leaf_out',function($data){
                return (collect($data->tc_rooting_transfer_bottle_works))->sum('leaf_work') -
                    (collect($data->tc_rooting_transfer_bottle_works))->sum('back_leaf');
                // return ($data->leaf_rooting - $data->leaf_left)-(collect($data->tc_rooting_transfer_bottle_works))->sum('back_leaf');
            })
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $data = TcRootingTransfer::select([
                'tc_rooting_transfers.*',
            ])
            ->where('tc_rooting_transfers.tc_init_id', $request->initId)
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
                $isAllow = TcRootingTransfer::delAllow($data);
                $el = '<div class="btn-group btn-group-sm">';
                $delEl = $printEl = $print2El = null;
                if($isAllow){
                    $delEl = '
                        <button type="button" class="btn btn-danger py-0" data-toggle="modal" data-target="#delModal" data-id="'.$data->id.'">Delete</button>
                    ';
                }

                $q = TcRootingTransfer::where('id',$data->id)->first();
                if($q->to_root1_bottle!=0 || $q->to_root2!=0){
                    $printEl = '
                        <button type="button" class="btn btn-primary py-0 printByTransfer" transfer-id="'.$data->id.'">Print</button>
                    ';
                }

                if($q->to_aclim!=0){
                    $print2El = '
                        <button type="button" class="btn btn-primary py-0 printPlant" transfer-id="'.$data->id.'">Print Aclim</button>
                    ';
                }

                $el .= $printEl.$delEl.$print2El;
                $el .= '</div>';
                return $el;
            })
            ->smart(false)
            ->toJson();
    }

    public function destroy($id)
    {
        $q = TcRootingTransferBottleWork::where('tc_rooting_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            $transBottleId = $value->tc_rooting_transfer_bottle_id;
            $bottleWork = $value->total_work;
            $leafWork = $value->leaf_work;
            $transferBottleWorkId = $value->id;
            TcRootingTransferBottle::where('id',$transBottleId)->update([
                'bottle_left' => DB::raw('bottle_left + '.$bottleWork),
                'leaf_left' => DB::raw('leaf_left + '.$leafWork),
            ]);
            TcRootingTransferBottleWork::where('id',$transferBottleWorkId)->forceDelete();
        }
        TcRootingTransfer::where('id',$id)->forceDelete();
        $q = TcRootingTransaction::select('tc_rooting_bottle_id')->where('tc_rooting_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            $bottleId = $value->tc_rooting_bottle_id;
            TcRootingBottle::where('id',$bottleId)->update(['status' => 1]);
        }
        TcRootingTransaction::where('tc_rooting_transfer_id',$id)->forceDelete();
        TcRootingTransferStock::where('tc_rooting_transfer_id',$id)->forceDelete();
        TcRootingBottle::where('tc_rooting_transfer_id',$id)->forceDelete();
        TcAclim::where('tc_rooting_transfer_id',$id)->forceDelete();
        TcAclimTree::where('tc_rooting_transfer_id',$id)->forceDelete();
        return alert(1,'Success, has been deleted.','alert-area-1');
    }
    public function parsingDataDel($id)
    {
        $q = TcRootingTransfer::select('id','transfer_date')
            ->where('id',$id)
            ->first();
        return response()->json([
            'attr' => Carbon::parse($q->transfer_date)->format('d/m/Y'),
            'id' => $q->id,
        ]);
    }
    // print label
    public function printByTransfer(Request $request){
        $data['title'] = "Print Label Rooting Transfer";
        $data['desc'] = "Print Label Transfer Date";
        $q = TcRootingTransfer::where('id',$request->id)->first();
        $q2 = TcRootingTransferStock::where('tc_rooting_transfer_id',$request->id)->get();
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
        return view('modules.rooting_transfer.print_label_layout',compact('data'));
    }

    public function printPlant(Request $request)
    {
        $data['title'] = "Print Label";
        $data['desc'] = "Print Label";

        $data['transfer'] = TcAclimTree::whereHas('tc_aclims',function($q) use($request){
                $q->where('tc_rooting_transfer_id',$request->id);
            })
            ->with([
                'tc_aclims',
                'tc_aclims.tc_inits' => function($q){
                    $q->select('id','tc_sample_id');
                },
                'tc_aclims.tc_inits.tc_samples' => function($q){
                    $q->select('id','sample_number');
                },
                'tc_aclims.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get()->toArray();
        return view('modules.rooting_transfer.print_label_layout2',compact('data'));
    }
}
