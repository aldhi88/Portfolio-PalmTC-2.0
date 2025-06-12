<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiationDelete;
use App\Models\TcCallusOb;
use App\Models\TcCallusObDetail;
use App\Models\TcInit;
use App\Models\TcInitBottle;
use App\Models\TcInitComment;
use App\Models\TcLaminar;
use App\Models\TcMediumStock;
use App\Models\TcRoom;
use App\Models\TcSample;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class InitController extends Controller
{
// index =====================================================
    public function index()
    {
        $data['title'] = "Initiation Data";
        $data['desc'] = "Display all available Initiation data";
        return view('modules.init.index', compact('data'));
    }
    public function dt()
    {
        $data = TcInit::select([
            "tc_inits.*",
            DB::raw('
                number_of_bottle * number_of_block AS total_bottle
            '),
            DB::raw('
            number_of_plant * number_of_bottle * number_of_block AS total_explant
            ') //note
        ])
        ->whereHas('tc_samples')
        ->with([
            'tc_samples.master_treefile',
            'tc_rooms',
            'tc_init_bottles',
        ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->editColumn("date",function($data){
                $mark = null;
                // dd($data->desc);
                if($data->desc == 'IMPORT DATA'){
                    $mark = "*";
                }
                $el = '<strong class="mt-0 font-size-14">'.Carbon::parse($data->created_at)->format("d/m/Y").$mark.'</strong><br>';
                if(is_null($data->date_stop)){
                    $el .= "
                        <p class='mb-0'><a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#nonActiveModal'>Non Active</a></p>
                    ";
                }else{
                    $el .= "
                        <p class='mb-0'><a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#activeModal'>Active</a></p>
                    ";
                }

                if(count($data->tc_init_bottles) != 0){
                    $el .= "
                            <p class='mb-0'><a class='text-primary' data-id='".$data->id."' href='".route('inits.show',$data->id)."'>View Detail</a></p>
                        ";
                }

                $el .= "
                            <p class='mb-0'><a class='text-primary' data-id='".$data->id."' href='".route('inits.comment',$data->id)."'>Comment</a></p>
                        ";
                $el .= "
                        <p class='mb-0'><a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteInitModal'>Delete</a></p>
                    ";
                return $el;
            })
            ->addColumn("action_bottle",function($data){
                $el = '<strong class="mt-0">'.$data->total_bottle.'</strong><br>';
                if(count($data->tc_init_bottles) != 0){
                    $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route("inits.indexBottle",$data->id)."'>Modify</a><br>
                    ";
                    $el .= "
                        <p class='mb-0'>
                            <a class='text-primary' href='".route("inits.indexPrintBottle",$data->id)."'>Print</a><br>
                    ";
                }

                $el .= '</p>';
                return $el;
            })
            ->addColumn('status_stop',function($data){
                $color = is_null($data->date_stop)?'primary':'secondary';
                $text = is_null($data->date_stop)?'active':Carbon::parse($data->date_stop)->format('d/m/y');
                $el = '
                    <span class="badge badge-'.$color.'">'.$text.'</span>
                ';
                return $el;
            })
            ->rawColumns(['date','medium','medium_stock_date','action_bottle','status_stop'])
            ->toJson();

    }

    public function create()
    {
        session()->forget([
            'session_init_step1',
            'session_init_step2',
            'session_init_step3',
        ]);
        $data['title'] = "Add New Initiation Data";
        $data['desc'] = "Form create new Initiation data";
        $data['sample_count'] = TcSample::all()->count();
        return view('modules.init.create', compact('data'));
    }
    // step1 =======================================================
    public function getStep1(Request $request)
    {
        $data['last_sample'] = TcSample::select('id','sample_number')
                ->latest('id')
                ->first();
        $data['rooms'] = TcRoom::all();
        $step1 = "step1_create";

        if(session()->has("session_init_step1")){
            $dtStep1 = session('session_init_step1');
            $q = TcSample::select('sample_number')
                ->where('id',$dtStep1['tc_sample_id'])
                ->first();
            $dtStep1['sample_number_show'] = $q->sample_number_display;
            $dtStep1['created_at_show'] = Carbon::parse($dtStep1['created_at'])->format('d/m/Y');
            $dtStep1['date_work_show'] = Carbon::parse($dtStep1['date_work'])->format('d/m/Y');
            $q = TcRoom::select('code')
                ->where('id',$dtStep1['tc_room_id'])
                ->first();
            $dtStep1['room_code'] = $q->code;
            $step1 = $dtStep1['form'];
            if(!is_null($request->page)){
                $step1 = $request->page;
                $dtStep1['form'] = $step1;
                session(['session_init_step1' => $dtStep1]);
            }
            return view('modules.init.create.'.$step1,compact('data','dtStep1'));
        }

        return view('modules.init.create.'.$step1,compact('data'));
    }
    public function submitStep1(Request $request)
    {
        $dtStep1 = $request->except('_token');
        $dtStep1['form'] = 'step1_read';
        $dtStep2['form'] = 'step2_create';
        $dtStep2['data'] = [];
        session([
            'session_init_step1' => $dtStep1,
            'session_init_step2' => $dtStep2
        ]);
    }
    public function updateStep1(Request $request)
    {
        $dtStep1 = $request->except('_token');
        $dtStep1['form'] = 'step1_read';
        session([
            'session_init_step1' => $dtStep1
        ]);
    }
    // step2 =======================================================
    public function getStep2(Request $request)
    {
        $step2 = "step2_blank";
        if(session()->has("session_init_step2")){
            $dtStep2 = session('session_init_step2');
            $step2 = $dtStep2['form'];
            $data['session'] = $dtStep2;
            $data['workers'] = TcWorker::query()->where('id','!=',0)->get();
            $data['laminars'] = TcLaminar::query()->where('id','!=',0)->get();
            $data['sessionWorkers'] = $dtStep2['data'];

            if(!is_null($request->page)){
                $step2 = $request->page;
                $dtStep2['form'] = $step2;
                session(['session_init_step2' => $dtStep2]);
            }
            return view('modules.init.create.'.$step2,compact('data'));
        }
        return view('modules.init.create.'.$step2);
    }
    public function addWorker(Request $request)
    {
        $dtStep2 = $request->except('_token');
        $dtStep2['laminar_code'] = TcLaminar::where('id',$request->tc_laminar_id)->first()->getAttribute('code');
        $dtStep2Session = session('session_init_step2');
        array_push($dtStep2Session['data'],$dtStep2);
        session(['session_init_step2' => $dtStep2Session]);
    }
    public function delWorker(Request $request)
    {
        $data = collect(session('session_init_step2')['data']);
        $remove = $request->tc_worker_id;
        $data = $data->filter(function($value,$key) use($remove){
            return $value['tc_worker_id'] != $remove;
        });

        $data = $data->toArray();
        $dtStep2['form'] = session('session_init_step2')['form'];
        $dtStep2['data'] = $data;

        session(['session_init_step2' => $dtStep2]);
    }
    public function finishStep2(Request $request)
    {
        if(count(session('session_init_step2')['data']) == 0){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area-step2',
                    'msg' => 'Error, please add worker for finish step 2.',
                ],
            ]);
        }
        $dtRead['numOfBlock'] = session('session_init_step1')['number_of_block'];
        $dtRead['numOfBottle'] = session('session_init_step1')['number_of_bottle'];
        $dtRead['numOfExplant'] = session('session_init_step1')['number_of_plant'];
        $dtRead['numOfWorker'] = count(session('session_init_step2')['data']);

        $dtRead['blockPerWorker'] = floor($dtRead['numOfBlock']/$dtRead['numOfWorker']);
        $dtRead['modulus'] = $dtRead['numOfBlock'] % $dtRead['numOfWorker'];
        $dtStep2['data'] = session('session_init_step2')['data'];
        $dtStep2['form'] = 'step2_read';
        $dtStep2['read'] = $dtRead;

        $dtStep3['form'] = 'step3_create';
        $dtStep3['data'] = [];
        session([
            'session_init_step2' => $dtStep2,
            'session_init_step3' => $dtStep3
        ]);

    }
    // step3 =======================================================
    public function getStep3(Request $request)
    {
        $step3 = "step3_blank";
        if(session()->has("session_init_step3")){
            $dtStep3 = session('session_init_step3');
            $step3 = $dtStep3['form'];
            $data['session'] = $dtStep3;
            $data["medium_stocks"] = TcMediumStock::with("tc_mediums")
                ->orderBy("created_at","desc")
                ->where('id','!=',99)
                ->get();

            if(!is_null($request->page)){
                $step3 = $request->page;
                $dtStep3['form'] = $step3;
                session(['session_init_step3' => $dtStep3]);
            }
            return view('modules.init.create.'.$step3,compact('data'));
        }
        return view('modules.init.create.'.$step3);
    }
    public function addStock(Request $request)
    {
        $dataForm = $request->except('_token');
        $mediumCode = $request->medium_code;
        $dataSession = session('session_init_step3');
        $stockId = $request->tc_medium_stock_id;
        $dtCollect = collect($dataSession['data']);

        // cek medium stok sama/tidak
        $count = $dtCollect->where('medium_code',$mediumCode)->count();
        if($count==0 && $dtCollect->count() !=0){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area-step3',
                    'msg' => 'Error, medium stock is not match each other.',
                ],
            ]);
        }

        $filter = $dtCollect->where('tc_medium_stock_id',$stockId);
        if($filter->count()==0){
            $q = TcMediumStock::where('id',$request->tc_medium_stock_id)->first();
            $dataForm['stock_date'] = $q->created_at_short_format;
            $dataForm['medium'] = $q->tc_mediums->code;
            $dataForm['bottle'] = $q->tc_bottles->code;
            $dataForm['agar'] = $q->tc_agars->code;
            array_push($dataSession['data'],$dataForm);
        }else{
            $dataSession['data'] = $dtCollect->filter(function($value,$key) use($stockId){
                return $value['tc_medium_stock_id'] != $stockId;
            })->toArray();
            $dtUpdate = $dtCollect->filter(function($value,$key) use($stockId){
                return $value['tc_medium_stock_id'] == $stockId;
            })->first();
            $dtUpdate['used_stock'] = $dtUpdate['used_stock'] + $request->used_stock;
            array_push($dataSession['data'],$dtUpdate);
        }

        session(['session_init_step3' => $dataSession]);

    }
    public function delStock(Request $request)
    {
        $data = collect(session('session_init_step3')['data']);
        $remove = $request->tc_medium_stock_id;
        $data = $data->filter(function($value,$key) use($remove){
            return $value['tc_medium_stock_id'] != $remove;
        });

        $data = $data->toArray();
        $dtStep3['form'] = session('session_init_step3')['form'];
        $dtStep3['data'] = $data;

        session(['session_init_step3' => $dtStep3]);
    }
    public function finishStep3(Request $request)
    {
        $dataStep3 = collect(session('session_init_step3')['data']);
        $numOfBottle = session('session_init_step1')['number_of_block'] * session('session_init_step1')['number_of_bottle'];
        $usedStock = $dataStep3->sum('used_stock');

        if($numOfBottle != $usedStock){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area-step3',
                    'msg' => 'Error, the number of bottles does not match.',
                ],
            ]);
        }

        $dtSessionStep3 = session('session_init_step3');
        $dtSessionStep3['form'] = "step3_read";
        session([
            'session_init_step3' =>$dtSessionStep3
        ]);
    }

    public function store(Request $request)
    {
        $dtSessionStep1 = session('session_init_step1');
        $dtSessionStep2 = session('session_init_step2');
        $dtSessionStep3 = session('session_init_step3');
        if(
            $dtSessionStep1['form'] != 'step1_read' ||
            $dtSessionStep2['form'] != 'step2_read' ||
            $dtSessionStep3['form'] != 'step3_read'
        ){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Please, finish all step before complate initiation.',
                ],
            ]);
        }

        // insert ke table tc_inits
        $dtInit['tc_sample_id'] = $dtSessionStep1['tc_sample_id'];
        $dtInit['tc_room_id'] = $dtSessionStep1['tc_room_id'];
        $dtInit['number_of_block'] = $dtSessionStep1['number_of_block'];
        $dtInit['number_of_bottle'] = $dtSessionStep1['number_of_bottle'];
        $dtInit['number_of_plant'] = $dtSessionStep1['number_of_plant'];
        $dtInit['desc'] = $dtSessionStep1['desc'];
        $dtInit['date_work'] = $dtSessionStep1['date_work'];
        $dtInit['created_at'] = $dtSessionStep1['created_at'];
        $lastId = TcInit::max('id') ?? 0; // ambil ID terakhir, default 0 kalau kosong
        $dtInit['id'] = $lastId + 1;
        DB::unprepared('SET IDENTITY_INSERT tc_inits ON');
        $q = TcInit::create($dtInit);
        DB::unprepared('SET IDENTITY_INSERT tc_inits OFF');
        $initId = $q->id;

        // insert ke table tc_callus_obs
        $dtObs['tc_init_id'] = $initId;
        $dtObs['date_schedule'] = Carbon::parse($dtInit['date_work'])->addMonths(3);
        $dtObs['date_ob'] = Carbon::parse($dtInit['date_work'])->addMonths(3);
        $dtObs['status'] = 0;
        TcCallusOb::create($dtObs);


        // persiapan data untuk ke table tc_init_bottles
        $workerIndex = 0;
        $workerCount = $dtSessionStep2['read']['numOfWorker'];

        for ($i=1; $i <= $dtInit['number_of_block'] ; $i++) {
            $blockNumber = $i;
            $data[] = [
                'tc_init_id' => $initId,
                'block_number' => $blockNumber,
                'tc_worker_id' => $dtSessionStep2['data'][$workerIndex]['tc_worker_id'],
                'tc_laminar_id' => $dtSessionStep2['data'][$workerIndex]['tc_laminar_id']
            ];
            $workerIndex++;
            $workerIndex = ($workerIndex==$workerCount)?0:$workerIndex;
        }
        // foreach ($dtSessionStep2['data'] as $key => $value) {
        //     $dtWorkerId[] = $value['tc_worker_id'];
        // }
        // $data = collect($data);
        // foreach ($dtWorkerId as $key => $value) {
        //     $workerId = $value;
        //     $dtAry[] = $data->where('tc_worker_id',$workerId)->toArray();
        // }
        // $dataOrder = call_user_func_array('array_merge', $dtAry);
        $dataOrder = $data;
        $index = 1;
        $indexStock = 0;
        $stockLoad = $dtSessionStep3['data'][$indexStock]['used_stock'];
        foreach ($dataOrder as $key => $value) {
            for ($i=$dtInit['number_of_bottle']; $i > 0 ; $i--) {
                if($stockLoad < $index){
                    $indexStock +=1;
                    $stockLoad = $stockLoad+($dtSessionStep3['data'][$indexStock]['used_stock']);
                }
                $dt480[] = [
                    "tc_init_id" => $value['tc_init_id'],
                    "block_number" => $value['block_number'],
                    "bottle_number" => $index,
                    "tc_worker_id" => $value['tc_worker_id'],
                    "tc_laminar_id" => $value['tc_laminar_id'],
                    "tc_medium_stock_id" => $dtSessionStep3['data'][$indexStock]['tc_medium_stock_id'],
                    "status" => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $index++;
            }

            try {
                TcInitBottle::insert($dt480);
                unset($dt480);
            } catch (Throwable $e) {
                report($e);
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'type' => 'danger',
                        'icon' => 'times',
                        'el' => 'alert-area',
                        'msg' => 'Failed, insert process is any trouble, contact your programmer.',
                    ],
                ]);
            }
        }
        unset($dataOrder);

        return response()->json([
            'status' => 'success',
            'data' => [
                'redirect' => route("inits.index"),
            ],
        ]);
    }

    public function show($id)
    {
        $data['title'] = "Add New Initiation Data";
        $data['desc'] = "Form create new Initiation data";

        $data['tc_init_id'] = $id;
        $data["initiations"] = TcInit::where("id", $id)
            ->first();
        $data["initiations"]->created_at_long_format = Carbon::parse($data['initiations']->created_at)->format("d M Y");
        $data["initiations"]->date_work_format = Carbon::parse($data['initiations']->date_work)->format("d M Y");

        // worker
        $dtWorker = TcInitBottle::select([
            'tc_init_bottles.*',
        ])
            ->with('tc_workers','tc_laminars','tc_inits')
            ->where('tc_init_id',$id)
            ->where('status',1)
            ->orderBy('block_number','asc')
            ->get()
            ->toArray();
        $dtWorker = collect($dtWorker);
        $dtWorker = $dtWorker->groupBy('tc_worker_id')->sortBy('block_number')->toArray();
        $dtWorker = array_values($dtWorker);

        $data['block_total'] = 0; $data['bottle_total'] = 0; $data['explant_total'] = 0;
        foreach ($dtWorker as $key => $value) {
            $value[0]['block_load'] = collect($value)->groupBy('block_number')->count();
            $value[0]['bottle_load'] = count($value);
            $value[0]['explant_load'] = count($value) * $value[0]['tc_inits']['number_of_plant'];
            $data['block_total'] = $data['block_total'] + $value[0]['block_load'];
            $data['bottle_total'] = $data['bottle_total'] + $value[0]['bottle_load'];
            $data['explant_total'] = $data['explant_total'] + $value[0]['explant_load'];
            $aryWorker[] = $value[0];
        }
        $data['worker'] = $aryWorker;

        // stock usage
        $q = TcInitBottle::select(['tc_medium_stock_id'])
            ->with([
                'tc_medium_stock_fast:id,tc_medium_id,tc_bottle_id,tc_agar_id,created_at',
                'tc_medium_stock_fast.tc_mediums:id,code',
                'tc_medium_stock_fast.tc_bottles:id,code',
                'tc_medium_stock_fast.tc_agars:id,code',
            ])
            ->where('tc_init_id',$id)
            ->get();
        $dt = $q->toArray();
        $dt = array_values(collect($dt)->groupBy('tc_medium_stock_id')->toArray());
        $total = 0;
        foreach ($dt as $key => $value) {
            $value[0]['stock_usage'] = count($value);
            $value[0]['tc_medium_stock_fast']['date_format'] = Carbon::parse($value['0']['tc_medium_stock_fast']['created_at'])->format('d/m/Y');
            $dtAry [] = $value[0];
            $total = $total + count($value);
        }
        // dd($dtAry);
        $data['init_stocks'] = $dtAry;
        $data['total'] = $total;

        // dd($data);
        return view('modules.init.show',compact('data'));
    }
    public function dtShow(Request $request){
        $initId=$request->id;
        $data = TcInitBottle::select(['*'])
            ->where('tc_init_id',$initId)
            ->with('tc_workers');
        return DataTables::eloquent($data)
            ->smart(false)
            ->toJson();
    }

    public function destroy(InitiationDelete $request, $id)
    {
        if(md5($request->pass_confirm) == $request->user()->password){
            try{
                TcInitBottle::where("tc_init_id",$id)->forceDelete();
                TcInit::where("id",$id)->forceDelete();
                TcCallusOb::where("tc_init_id",$id)->forceDelete();
                TcCallusObDetail::where("tc_init_id",$id)->forceDelete();
            }catch(Throwable $e){
                report($e);
                return false;
            }
        }else{
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Error, confirmation password is not match.',
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data deleted successfully.',
            ],
        ]);
    }
    public function getDataDelete(Request $request){
        $initId = $request->id;
        $data = TcInit::where("id",$initId)
            ->with("tc_samples")
            ->first();
        $return["init_date"] = Carbon::parse($data->created_at)->format("d M Y");
        $return["sample_number"] = $data->tc_samples->sample_number_display;
        return response()->json([
            'status' => 'success',
            'data' => [
                'return' => $return,
            ],
        ]);
    }
    public function active(Request $request){
        $initId = $request->id;
        TcInit::where('id',$initId)
            ->update([
                'date_stop' => null
            ]);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, process has been successfully.',
            ],
        ]);
    }
    public function nonActive(Request $request){
        $data = $request->except('_token','id');
        $initId = $request->id;
        TcInit::where('id',$initId)
            ->update($data);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, process has been successfully.',
            ],
        ]);
    }

    // botol olah
    public function indexBottle(Request $request){
        $data['title'] = "Edit Bottle Data";
        $data['desc'] = "Form edit number of bottle";
        $data['tc_init_id'] = $request->id;
        $initId = $request->id;
        $dtWorker = TcInitBottle::select(['tc_init_bottles.*'])
            ->with('tc_workers')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->get()
            ->toArray();
        $dtWorker = array_values(collect($dtWorker)->groupBy('tc_worker_id')->toArray());
        $data['worker'] = $dtWorker;

        $dtStock = TcInitBottle::select(['tc_medium_stock_id'])
            ->with('tc_medium_stock_fast:id,created_at')
            ->where('tc_init_id',$initId)
            ->get()
            ->toArray();
        $dtStock = array_values(collect($dtStock)->groupBy('tc_medium_stock_id')->toArray());
        foreach ($dtStock as $key => $value) {
            $value[0]['tc_medium_stock_fast']['date_short'] = Carbon::parse($value[0]['tc_medium_stock_fast']['created_at'])->format('d/m/Y');
            $aryStock[] = $value[0];
        }
        $data['stocks'] = $aryStock;
        return view("modules.init.bottle",compact("data"));
    }
    public function dtBottleSummary(Request $request){
        $initId = $request->id;
        $dtWorker = TcInitBottle::select([
            'tc_init_bottles.*',
        ])
            ->with('tc_workers','tc_laminars','tc_inits')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->get()
            ->toArray();
        $dtWorker = collect($dtWorker);
        $dtWorker = $dtWorker->groupBy('tc_worker_id')->toArray();
        $dtWorker = array_values($dtWorker);

        $data['block_total'] = 0; $data['bottle_total'] = 0; $data['explant_total'] = 0;
        foreach ($dtWorker as $key => $value) {
            $value[0]['block_load'] = collect($value)->groupBy('block_number')->count();
            $value[0]['bottle_load'] = count($value);
            $value[0]['explant_load'] = count($value) * $value[0]['tc_inits']['number_of_plant'];
            $data['block_total'] = $data['block_total'] + $value[0]['block_load'];
            $data['bottle_total'] = $data['bottle_total'] + $value[0]['bottle_load'];
            $data['explant_total'] = $data['explant_total'] + $value[0]['explant_load'];
            $aryWorker[] = $value[0];
        }
        $data['worker'] = $aryWorker;
        return view("modules.init.include.bottle_summary",compact("data"));
    }
    public function addBlockOption(Request $request){
        $initId = $request->init_id;
        $workerId = $request->worker_id;
        $dtBlock = TcInitBottle::select(['tc_init_bottles.*'])
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->where('tc_worker_id',$workerId)
            ->get()
            ->toArray();
        $dtBlock = array_values(collect($dtBlock)->groupBy('block_number')->toArray());
        $data['blocks'] = $dtBlock;
        return view("modules.init.include.block_option", compact("data"));
    }
    public function formAddBottleWorker(Request $request){
        $data['tc_init_id'] = $request->tc_init_id;
        $data['block_number'] = $request->block_number;
        $data['tc_worker_id'] = $request->tc_worker_id;
        $data['tc_laminar_id'] = TcInitBottle::getLaminar($data['tc_init_id'],$data['tc_worker_id']);
        $data['tc_medium_stock_id'] = $request->tc_medium_stock_id;
        $data['status'] = 1;
        $nextNumber = TcInitBottle::getLastBottleNumber($data['tc_init_id']);
        $loopBottle = $request->number_of_bottle;
        for ($i=1; $i <= $loopBottle ; $i++) {
            $data['bottle_number'] = $nextNumber+$i;
            TcInitBottle::create($data);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area-addBottle',
                'msg' => 'Success, bottle has been added',
            ],
        ]);
    }
    public function dtBottle(Request $request){
        $initId=$request->id;
        $data = TcInitBottle::select(['*'])
            ->where('tc_init_id',$initId)
            ->with('tc_workers');
        // $data = $TcInitOfBottle->selDtBottle($request->id);
        return DataTables::eloquent($data)
            ->editColumn("status_control",function($data){

                if($data->status==1){
                    $badge = "on text-primary";
                }else{
                    $badge = "off text-secondary";
                }
                return '<i data-id="'.$data->id.'" data-status="'.$data->status.'" class="switch fas fa-toggle-'.$badge.'"></i>';
            })
            ->rawColumns(["status_control"])
            ->smart(true)
            ->toJson();
    }
    public function changeBottleStatus(Request $request){
        TcInitBottle::where("id",$request->id)
            ->update(["status" => $request->status]);
        return response()->json([
            'status' => 'success',
            'data' => [
                'status' => $request->status,
                'id' => $request->id,
            ],
        ]);
    }

    // print label
    public function indexPrintBottle($id){
        $data['title'] = "Print Bottle Label (Initiation)";
        $data['desc'] = "In this page you can print bottle's label";
        $data['tc_init_id'] = $id;

        $data["initiations"] = TcInit::where("id", $id)
            ->first();
        $data["initiations"]->created_at_long_format = Carbon::parse($data['initiations']->created_at)->format("d M Y");
        $data["initiations"]->date_work_format = Carbon::parse($data['initiations']->date_work)->format("d M Y");

        $data["max_bottle_number"] = TcInitBottle::where("tc_init_id",$id)
            ->where('status',1)
            ->orderBy('bottle_number','desc')
            ->first()
            ->getAttribute('bottle_number');
        $data["max_block_number"] = TcInitBottle::where("tc_init_id",$id)
            ->where('status',1)
            ->orderBy('block_number','desc')
            ->first()
            ->getAttribute('block_number');

        $data['workers'] = TcInitBottle::select('tc_worker_id','bottle_number')
            ->orderBy('bottle_number','asc')
            ->with('tc_workers:id,code')
            ->get()
            ->toArray();
        $data['workers'] = collect($data['workers']);
        $data['workers'] = array_values($data['workers']->groupBy('tc_worker_id')->toArray());
        foreach ($data['workers'] as $key => $value) {
            $dtAry[] = $value[0];
        }
        $data['workers'] = $dtAry;

        return view('modules.init.print_botol', compact('data'));
    }
    public function printByBottleNumber(Request $request){
        $data['title'] = "Print Label";
        $data['desc'] = "Print Label By Bottle Number";
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        $initId = $request->init;
        $data["bottles"] = TcInitBottle::select([
                'id','block_number','bottle_number','tc_init_id','tc_worker_id','tc_medium_stock_id'
            ])
            ->where("tc_init_id",$initId)
            ->whereBetween("bottle_number",[$request->from,$request->to])
            ->where('status',1)
            ->with([
                'tc_inits:id,tc_sample_id',
                'tc_inits.tc_samples' => function($q){
                    $q->select('id','sample_number');
                },
                'tc_medium_stocks_min:id,tc_agar_id',
                'tc_medium_stocks_min.tc_agars' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        // dd($data['bottles'][0]->toArray());
        if($request->type == 1){
            return view("modules.init.print.print_label_botol_layout", compact('data'));
        }else{
            return view("modules.init.print.print_label_botol_layout2", compact('data'));
        }
    }
    public function printByBlockNumber(Request $request){
        $data['title'] = "Print Label";
        $data['desc'] = "Print Label By Block Number";
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        $initId = $request->init;
        $data["bottles"] = TcInitBottle::where("tc_init_id",$initId)
            ->whereBetween("block_number",[$request->from,$request->to])
            ->where('status',1)
            ->with("tc_inits",function($q){
                $q->with("tc_samples",function($q1){
                    $q1->with("master_treefile");
                });
            })
            ->with("tc_medium_stocks",function($q3){
                $q3->with("tc_mediums","tc_agars");
            })
            ->get();
        $q = TcInit::select("date_work")
            ->where("id",$initId)
            ->first();
        $data["date_of_work"] = Carbon::parse($q->date_work)->format("d F Y");

        return view("modules.init.print.print_label_botol_layout", compact('data'));
    }
    public function printByWorker(Request $request){
        $data['title'] = "Print Label";
        $data['desc'] = "Print Label By Worker";
        $data['worker'] = $request->worker;
        $initId = $request->init;
        $data["bottles"] = TcInitBottle::where("tc_init_id",$initId)
            ->where("tc_worker_id",$request->worker)
            ->where('status',1)
            ->with("tc_inits",function($q){
                $q->with("tc_samples",function($q1){
                    $q1->with("master_treefile");
                });
            })
            ->with("tc_medium_stocks",function($q3){
                $q3->with("tc_mediums","tc_agars");
            })
            ->get();
        // dd($data["bottles"]->toArray());
        $q = TcInit::select("date_work")
            ->where("id",$initId)
            ->first();
        $data["date_of_work"] = Carbon::parse($q->date_work)->format("d F Y");
        return view("modules.init.print.print_label_botol_layout", compact('data'));
    }
    public function dtPrintByCheck(Request $request){
        $data = TcInitBottle::select('tc_init_bottles.*')
            ->where('tc_init_bottles.status',1)
            ->where('tc_init_id',$request->id)
            ->with("tc_workers");
        return DataTables::eloquent($data)
            ->addColumn("actionColumn",function($data){
                $dataPrint = session("data_bottle");
                $bottleId = $data->id;
                $checked = null;
                if(!is_null($dataPrint)){
                    foreach ($dataPrint as $key => $value) {
                        $val = $value;
                        if($val == $bottleId){
                            $checked = "checked";
                            break;
                        }
                    }
                }
                return '<input '.$checked.' class="check-bottle" type="checkbox" name="bottle_id[]" value="'.$data->id.'">';
            })
            ->rawColumns(["actionColumn"])
            ->smart(true)
            ->toJson();
    }
    public function checkBottlePrint(Request $request){
        $bottleId = $request->bottleId;
        $status = $request->status;

        $dataAry = session('data_bottle');

        if($status==1){
            $data = $dataAry;
            if(is_null($data)){
                $data = array("bottleId_".$bottleId => $bottleId);
            }else{
                $data["bottleId_".$bottleId] = $bottleId;
            }
            session(["data_bottle" => $data]);
        }else{
            $data = $dataAry;
            unset($data["bottleId_".$bottleId]);
            session(["data_bottle" => $data]);
        }

    }
    public function dataPrintCustom(){
        // data session print custom
        if(!is_null(session("data_bottle"))){
            $idBottle = [];
            foreach (session("data_bottle") as $key => $value) {
                array_push($idBottle,$value);
            }
            $data["print_bottle_custom"] = TcInitBottle::whereIn("id", $idBottle)
                ->get();
        }else{
            $data["print_bottle_custom"] = null;
        }

        return view("modules.init.print_bottle_list", compact("data"));
    }
    public function dataPrintCustomUncheckAll(){
        session()->forget("data_bottle");
    }
    public function checkBeforePrintCheck(Request $request){
        if(
            is_null(session("data_bottle")) ||
            !$request->session()->has('data_bottle')
        ){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area-c',
                    'msg' => 'Sorry, please select data print first.',
                ],
            ]);
        }else{
            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => 'success',
                    'icon' => 'check',
                    'el' => 'alert-area-c',
                    'msg' => 'Success, new data has been added.',
                ],
            ]);
        }
    }
    public function triggerPrintCheck(Request $request){
        $data['title'] = "Print Label";
        $data['desc'] = "Print Label By Bottle Number";

        $idBottle = [];
        foreach (session("data_bottle") as $key => $value) {
            array_push($idBottle,$value);
        }

        $initId = $request->init;
        $data["bottles"] = TcInitBottle::where("tc_init_id",$initId)
            ->whereIn("id", $idBottle)
            ->with("tc_inits",function($q){
                $q->with("tc_samples",function($q1){
                    $q1->with("master_treefile");
                });
            })
            ->get();

        $q = TcInit::select("date_work")
            ->where("id",$initId)
            ->first();
        $data["date_of_work"] = Carbon::parse($q->date_work)->format("d F Y");

        return view("modules.init.print.print_label_botol_layout", compact('data'));
    }

    public function comment($id)
    {
        $data['title'] = "Initiation Comments - Files - Images";
        $data['desc'] = "Manage data comment, file and image";
        $data['initId'] = $id;
        return view('modules.init.comment', compact('data'));
    }

    public function dtComment(Request $request)
    {
        $data = TcInitComment::select([
            'tc_init_comments.*',
            DB::raw('convert(varchar,created_at, 103) as created_at_format'), //note*
        ])
            ->where('tc_init_id',$request->id)
            // ->with(['tck_acclims:id'])
        ;
        // if($request->filter==1){
        //     $data->whereNull('file')->whereNull('image');
        // }else if($request->filter==2){
        //     $data->whereNull('image');
        // }else if($request->filter==3){
        //     $data->whereNull('file');
        // }
        return Datatables::of($data)
            ->addColumn('action', function($data){
                // $el = '
                //     <a class="text-primary fs-13" data-id="'.$data->id.'" href="#" data-toggle="modal" data-target="#editCommentModal">Edit</a>
                // ';
                $dtJson['comment'] = $data->comment;
                $dtJson['id'] = $data->id;
                $json = json_encode($dtJson);
                $el = '
                    <a class="text-danger fs-13" data-json=\''.htmlspecialchars(json_encode($json), ENT_QUOTES, 'UTF-8').'\' href="#" data-toggle="modal" data-target="#deleteCommentModal">Delete</a>
                ';
                return $el;
            })
            ->filterColumn('created_at_format', function($query, $keyword){
                $sql = 'convert(varchar,created_at, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('image_file', function($data){
                $el = null;
                if(!is_null($data->file)){
                    $el = '
                        <a href="'.asset("storage/media/init/file").'/'.$data->file.'">
                            <h5><i class="feather mr-2 icon-file"></i>Download</h5>
                        </a>
                    ';
                }

                return $el;
            })
            ->addColumn('image_format', function($data){
                $el = null;
                if(!is_null($data->image)){
                    $el = '
                        <a href="'.asset("storage/media/init/image").'/'.$data->image.'" target="_blank">
                        <img src="'.asset("storage/media/init/image").'/'.$data->image.'" class="img-thumbnail" width="70">
                        </a>
                    ';
                }
                return $el;
            })
            ->rawColumns(['image_format','image_file','action'])
            ->smart(false)->toJson();
    }


    public function commentStore(Request $request)
    {
        $dt = $request->except('_token','file','image');
        if ($request->hasFile('file')) {
            $dt['file'] = Str::uuid() . '.' . ($request->file('file'))->getClientOriginalExtension();
            ($request->file('file'))->storeAs('public/media/init/file', $dt['file']);
        }
        if ($request->hasFile('image')) {
            $dt['image'] = Str::uuid() . '.' . ($request->file('image'))->getClientOriginalExtension();
            ($request->file('image'))->storeAs('public/media/init/image', $dt['image']);
        }

        TcInitComment::create($dt);

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been added.',
            ],
        ]);
    }

    public function commentDestroy(Request $request)
    {
        $data = TcInitComment::find($request->id);
        Storage::delete('public/media/init/file/'.$data->file);
        Storage::delete('public/media/init/image/'.$data->image);
        TcInitComment::find($request->id)->delete();
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data has been deleted.',
            ],
        ]);
    }
}
