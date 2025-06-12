<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallusTransferCreate;
use App\Models\TcCallusOb;
use App\Models\TcCallusTransfer;
use App\Models\TcCallusTransferBottle;
use App\Models\TcCallusTransferStock;
use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoObDetail;
use App\Models\TcInit;
use App\Models\TcLaminar;
use App\Models\TcMediumStock;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CallusTransferController extends Controller
{
    public function index()
    {
        $data['title'] = "Callus Transfer Data";
        $data['desc'] = "Display all bottle can be transfer by sample data";
        $q = TcCallusOb::get()->count();
        $data['dtObs'] = $q==0? false:true;
        return view('modules.callus_transfer.index',compact('data'));
    }
    public function dt(){
        $data = TcInit::select('tc_inits.id','tc_inits.tc_sample_id')
            ->withCount([
                'tc_callus_obs as total_ob' => function(Builder $q){
                    $q->where('status',1);
                }
            ])
            ->withCount([ //note!
                'tc_callus_obs as total_callus' => function(Builder $q){
                    $q->select(DB::raw("SUM(bottle_callus) as bottlecallus"));
                }
            ])
            ->whereHas('tc_callus_obs')
            ->with([
                'tc_samples' => function($q){
                    $q->select('id','sample_number');
                },
            ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->editColumn('sample_action', function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('callus-transfers.detail',$data->id)."'>View</a>
                ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('date',function($data){
                return Carbon::parse($data->created_at)->format('d/m/Y');
            })
            ->addColumn('transferred',function($data){
                $transfered = TcCallusTransfer::where('tc_init_id',$data->id)->sum('bottle_used');
                return $transfered;
            })
            ->addColumn('bottle_left',function($data){
                $totalBottle = $data->total_callus;
                $transfered = TcCallusTransfer::where('tc_init_id',$data->id)->sum('bottle_used');
                return $totalBottle-$transfered;
            })
            ->rawColumns(['sample_action'])
            ->toJson();
    }

    public function detail($id){
        $data['title'] = "Detail Transfer";
        $data['desc'] = "Show all transfer data of observations";
        $q = TcInit::select("tc_inits.id",'tc_inits.tc_sample_id')
            ->where('id',$id)
            ->withCount([
                'tc_callus_obs as total_ob' => function(Builder $q){
                    $q->where('status',1);
                }
            ])
            ->withCount([
                'tc_callus_obs as total_callus' => function(Builder $q){
                    $q->select(DB::raw("SUM(bottle_callus) as bottlecallus"));
                }
            ])
            ->whereHas('tc_callus_obs')
            ->with([
                'tc_samples' => function($q){
                    $q->select('id','sample_number');
                },
            ])
            ->first();

        $data['initId'] = $id;
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['totalObs'] = $q->total_ob;

        $data['totalBottleCallus'] = $q->total_callus;
        $data['bottleTransfered'] = TcCallusTransfer::where('tc_init_id',$q->id)->sum('bottle_used');
        $data['bottleLeft'] = $data['totalBottleCallus'] - $data['bottleTransfered'];

        return view("modules.callus_transfer.detail_transfer",compact('data'));
    }
    public function dtDetailTransfer(Request $request){
        $data = TcCallusOb::select([
                "tc_callus_obs.*",
                DB::raw('ROW_NUMBER() OVER(ORDER BY date_ob ASC) AS rownum')
            ])
            ->where('tc_init_id',$request->initId)
            ->where('status', 1)
            ->with([
                'tc_callus_ob_details' => function($q){
                    $q->select('id','tc_callus_ob_id')
                        ->where('result',1);
                }
            ])
        ;

        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('date_obs_format', function($data) use($request){
                $el = '<strong class="mt-0 font-size-14">'.Carbon::parse($data->date_ob)->format('d/m/Y').'</strong>';
                return $el;
            })
            ->addColumn('transfer',function($data){
                $el = '
                    <div class="btn-group">
                        <a href="'.route('callus-transfers.create',$data->id).'" class="btn rounded-0 py-0 btn-primary btn-sm"><i class="feather icon-repeat"></i> Transfer</a>
                    </div>
                ';
                return $el;
            })
            ->addColumn('transfered',function($data){
                $transfered = TcCallusTransfer::where('tc_callus_ob_id',$data->id)->sum('bottle_used');
                return $transfered;
            })
            ->addColumn('bottle_left',function($data){
                $totalBottle = $data->bottle_callus;
                $transfered = TcCallusTransfer::where('tc_callus_ob_id',$data->id)->sum('bottle_used');
                return $totalBottle-$transfered;
            })
            ->editColumn('rownum',function($data){
                return '#'.$data->rownum;
            })
            ->addIndexColumn()
            ->rawColumns(['date_obs_format','transfer'])
            ->toJson();
    }
    public function dtListTransferPerInit(Request $request){
        $qCode = 'convert(varchar,tc_callus_obs.date_ob, 103)';
        if(config('database.default') == 'mysql'){
            $qCode = 'DATE_FORMAT(tc_callus_obs.date_ob, "%d/%m/%Y")';
        }
        $data = TcCallusTransfer::select([
                'tc_callus_transfers.*',
                DB::raw($qCode.' as date_ob_format')
            ])
            ->leftJoin('tc_callus_obs','tc_callus_obs.id','=','tc_callus_transfers.tc_callus_ob_id')
            ->where('tc_callus_transfers.tc_init_id',$request->id)
            ->with('tc_workers','tc_laminars','tc_callus_transfer_bottles','tc_callus_obs');
        return DataTables::of($data)
            ->filterColumn('date_ob_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('date_work_format',function($data){
                return Carbon::parse($data->date_work)->format('d/m/Y');
            })
            ->addColumn('index_number',function($data){
                $data = collect($data->tc_callus_transfer_bottles->toArray());
                $firstNumber = $data->first()['bottle_number'];
                $lastNumber = $data->last()['bottle_number'];
                return $firstNumber.' - '.$lastNumber;
            })
            ->addColumn('subculture',function(){
                return 1;
            })
            ->rawColumns([])
            ->smart(false)
            ->toJson();
    }

    public function printBlankForm(Request $request){
        $data['title'] = "Print Blank Callus Transfer Form";
        $data['desc'] = "Printing transfer form before input transfer result";
        $id = $request->initId;
        if($request->page == 1){
            $data['totalRow'] = $request->page * 25;
        }else{
            $data['totalRow'] = ($request->page * 27) - 2;
        }
        return view('modules.callus_transfer.print.form_blank',compact('data'));
    }

    public function create($id){

        $data['title'] = "Callus Transfer Form";
        $data['desc'] = "Input new transfer per observation";

        $dtObs = TcCallusOb::where('id',$id)
            ->first();

        $data['initId'] = $dtObs->tc_init_id;
        $data['sampleNumber'] = $dtObs->tc_inits->tc_samples->sample_number_display;
        $data['initDate'] = Carbon::parse($dtObs->tc_inits->created_date)->format('d/m/Y');
        $data['selNumber'] = $dtObs->tc_inits->tc_samples->master_treefile->noseleksi;
        $data['plantingYear'] = $dtObs->tc_inits->tc_samples->master_treefile->tahuntanam;
        $data['type'] = $dtObs->tc_inits->tc_samples->master_treefile->tipe;
        $data['program'] = $dtObs->tc_inits->tc_samples->program;
        $data['obsDate'] = Carbon::parse($dtObs->date_ob)->format('d/m/Y');
        $data['obsId'] = $dtObs->id;

        $data['totalBottleCallus'] = $dtObs->bottle_callus;
        $data['bottleTransfered'] = TcCallusTransfer::where('tc_callus_ob_id',$id)->sum('bottle_used');
        $data['bottleLeft'] = $data['totalBottleCallus'] - $data['bottleTransfered'];

        $data['worker'] = TcWorker::where('status',1)->where('id','!=',0)->get();
        $data['laminar'] = TcLaminar::where('id','!=',0)->get();
        $data['medium_stock'] = TcMediumStock::with("tc_mediums")
            ->orderBy("created_at","desc")
            ->get();

        session(['medium_stock' => []]);
        $data['medium_stock'] = session('medium_stock');

        return view('modules.callus_transfer.create',compact('data'));
    }
    public function dtCallusTransfer(Request $request){
        $data = TcCallusTransfer::select([
                'tc_callus_transfers.*'
            ])
            ->where('tc_callus_ob_id',$request->id)
            ->with('tc_workers','tc_laminars','tc_callus_transfer_bottles');
        return DataTables::of($data)
            ->addColumn('date_work_format',function($data){
                return Carbon::parse($data->date_work)->format('d/m/Y');
            })
            ->addColumn('index_number',function($data){
                $data = collect($data->tc_callus_transfer_bottles->toArray());
                $firstNumber = $data->first()['bottle_number'];
                $lastNumber = $data->last()['bottle_number'];
                return $firstNumber.' - '.$lastNumber;
            })
            ->addColumn('delete',function($data){
                $embryoId = TcEmbryoBottle::where('tc_callus_transfer_id',$data->id)->first()->getAttribute('id');
                $q = TcEmbryoObDetail::where('tc_embryo_bottle_id',$embryoId)->get()->count();
                $elDel = null;
                if($q==0){
                    $elDel = '<button class="py-0 px-1 btn btn-sm btn-danger"><i class="feather icon-x"></i></button>';
                }
                $el = '
                    <form class="delTransfer">
                    '.csrf_field().'
                    <input type="hidden" name="id" value="'.$data->id.'">
                    <button type="button" class="py-0 px-1 btn btn-sm btn-primary printByTransfer" transfer-id="'.$data->id.'"><i class="feather icon-printer"></i></button>
                    '.$elDel.'
                    </form>
                ';
                return $el;
            })
            ->rawColumns(['delete'])
            ->smart(false)
            ->toJson();
    }
    public function getDateList(Request $request){
        $data['dateList'] = TcCallusTransfer::select('date_work')
            ->where('tc_callus_ob_id',$request->obsId)
            ->groupBy('date_work')
            ->orderBy('date_work','desc')
            ->get();
        return view('modules.callus_transfer.date_list',compact('data'));
    }
    public function store(CallusTransferCreate $request)
    {
        $dtTransfer = $request->except('_token');
        $q = TcCallusTransfer::create($dtTransfer);
        $transferId = $q->id;
        $obsId = $q->tc_callus_ob_id;
        $initId = TcCallusOb::select("tc_init_id")
            ->where('id',$obsId)
            ->first()
            ->getAttribute('tc_init_id');
        $dtBottle = session('medium_stock');
        $data['tc_init_id'] = $initId;
        $data['tc_callus_transfer_id'] = $transferId;
        $data['date_work'] = $request->date_work;

        $dtEmbryo['tc_init_id'] = $initId;
        $dtEmbryo['tc_worker_id'] = $request->tc_worker_id;
        $dtEmbryo['tc_laminar_id'] = $request->tc_laminar_id;
        $dtEmbryo['bottle_date'] = $request->date_work;



        // bottle number
        $count = TcCallusTransferBottle::where('tc_init_id',$initId)->get()->count();
        $nextNumber = $count + 1;
        $dtEmbryo['tc_callus_transfer_id'] = $transferId;
        $totalBottle = 0;
        foreach ($dtBottle as $key => $value) {
            $data['tc_medium_stock_id'] = $value['id'];
            $data['bottle_number'] = $nextNumber;
            $data['tc_callus_ob_id'] = $request->tc_callus_ob_id;
            $totalBottle = $totalBottle+$value['stock_used'];
            $dtEmbryo['number_of_bottle'] = $totalBottle;
            for ($i=1; $i <=$value['stock_used'] ; $i++) {
                TcCallusTransferBottle::create($data);
                $data['bottle_number'] += 1;
            }

            $nextNumber = $data['bottle_number'];

            $datax[] = [
                "tc_callus_transfer_id" => $transferId,
                "tc_medium_stock_id" => $value['id'],
                "stock_used" => $value['stock_used'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        TcEmbryoBottle::create($dtEmbryo);
        $q = TcEmbryoBottle::where('tc_init_id',$initId)
            ->get()
            ->count();
        TcCallusTransferStock::insert($datax);
        session(['medium_stock' => []]);
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
    public function setBottleLeft(Request $request){
        $dtObs = TcCallusOb::where('id',$request->id)
            ->first();
        $data['totalBottleCallus'] = $dtObs->bottle_callus;
        $data['bottleTransfered'] = TcCallusTransfer::where('tc_callus_ob_id',$request->id)->sum('bottle_used');
        $data['bottleLeft'] = $data['totalBottleCallus'] - $data['bottleTransfered'];
        return response()->json([
            'status' => 'success',
            'data' => [
                'left' => $data['bottleLeft'],
            ],
        ]);
    }


    public function delTransfer(Request $request){
        $id = $request->id;
        $q = TcEmbryoBottle::where('tc_callus_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            $aryId[] = $value->id;
        }
        $q = TcEmbryoObDetail::whereIn('tc_embryo_bottle_id',$aryId)->get()->count();
        if($q==0){ //jika data embryo bottle belum diobservasi
            // rearrange number of bottle
            $q = TcCallusTransferBottle::select('bottle_number')
                ->where('tc_callus_transfer_id',$request->id)
                ->orderBy('bottle_number','desc')->get();
            $countBottle = count($q);
            $lastNumber = $q->first()->bottle_number;
            TcCallusTransferBottle::where('bottle_number','>',$lastNumber)
                ->decrement('bottle_number',$countBottle);
            // delete related data
            TcCallusTransfer::where('id',$id)->forceDelete();
            TcCallusTransferBottle::where('tc_callus_transfer_id',$id)->forceDelete();
            TcCallusTransferStock::where('tc_callus_transfer_id',$id)->forceDelete();
            TcEmbryoBottle::where('tc_callus_transfer_id',$id)->forceDelete();
            return alert(1,null,null);
        }else{
            return alert(0,'Failed, related data is still in use.',null);
        }
    }

    public function dtPickedMedStock(Request $request){
        $data = collect($request->session()->get('medium_stock'));
        return DataTables::of($data)
            ->addColumn("delete",function($data){
                $el = '
                    <form class="deletePickedMedStock">
                    '.csrf_field().'
                    <input type="hidden" name="id" value="'.$data['id'].'">
                    <button class="py-0 px-1 btn btn-sm btn-danger"><i class="feather icon-x"></i></button>
                    </form>
                ';
                return $el;
            })
            ->rawColumns(["delete"])
            ->toJson();
    }
    public function dtPickMedStock(){
        $q = TcMediumStock::select([
                'tc_medium_stocks.*',
            ])
            ->with("tc_mediums","tc_bottles","tc_agars");

        $data = collect();
        foreach ($q->get() as $key => $value) {
            if($value->current_stock > 0){
                $data->push([
                    'id' => $value->id,
                    'created_at_short_format' => $value->created_at_short_format,
                    'created_at' => Carbon::parse($value->created_at)->format('Ymd'),
                    'tc_mediums_code' => $value->tc_mediums->code,
                    'tc_bottles_code' => $value->tc_bottles->code,
                    'tc_agars_code' => $value->tc_agars->code,
                    'current_stock' => $value->current_stock,
                ]);
            }
        }

        return DataTables::of($data)
            ->editColumn('current_stock',function($data){
                $stock = collect(session()->get('medium_stock'));
                $stockId = $data['id'];
                $check = $stock->where('id',$stockId);
                $return = number_format($data['current_stock'],'0',',','.');
                if(count($check) > 0){
                    $return = $data['current_stock'] - $check->first()['stock_used'];
                    $return = number_format($return,'0',',','.');
                }
                return $return;
            })
            ->addColumn('form',function($data){
                $el = '
                <form class="addStock">
                    '.csrf_field().'
                    <input type="hidden" name="id" value="'.$data['id'].'">
                    <input type="hidden" name="created_at_short_format" value="'.$data['created_at_short_format'].'">
                    <input type="hidden" name="tc_mediums_code" value="'.$data['tc_mediums_code'].'">
                    <input type="hidden" name="tc_bottles_code" value="'.$data['tc_bottles_code'].'">
                    <input type="hidden" name="tc_agars_code" value="'.$data['tc_agars_code'].'">
                    <div class="input-group">
                        <input type="number" name="stock_used" value="1" required min="1" max="'.$data['current_stock'].'" class="form-control form-control-sm">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-plus"></i></button>
                        </div>
                    </div>
                </form>
                ';
                return $el;
            })
            ->rawColumns(["form"])
            ->smart(false)
            ->toJson();
    }

    public function deletePickedMedStock(Request $request){
        $data = collect($request->session()->get('medium_stock'));
        $dataDel = $data->where('id', '!=', $request->id)->toArray();
        session([
            'medium_stock' => $dataDel
        ]);
    }
    public function storeStock(Request $request){
        $oldData = collect($request->session()->get('medium_stock'));
        $check = $oldData->where('id',$request->id);
        if(count($check) > 0){
            $oldStock = $check->first()['stock_used'];
            $newStock = $oldStock + $request->stock_used;
            $formData = collect($check->first());
            $formData = $formData->replace(['stock_used' => $newStock]);
            $oldData = $oldData->where('id','!=',$request->id);
            $formData = $formData->toArray();
        }else{
            $formData = [
                "id" => $request->id,
                "created_at_short_format" => $request->created_at_short_format,
                "tc_mediums_code" => $request->tc_mediums_code,
                "tc_bottles_code" => $request->tc_bottles_code,
                "tc_agars_code" => $request->tc_agars_code,
                "stock_used" => $request->stock_used,
            ];
        }
        $data = $oldData->push($formData);
        session(['medium_stock' => $data]);
    }
    public function getCountMedStock(){
        $data = collect(session('medium_stock'));
        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $data->sum('stock_used'),
            ],
        ]);
    }

    // print label
    public function printByGroup(Request $request){
        $data['title'] = "Print Label Sub Cultur 1";
        $data['desc'] = "Print Label By Date Group";
        $q = TcCallusTransferBottle::where('date_work',$request->dateWork)
            ->orderBy('bottle_number','asc')
            ->get();

        $data['bottles'] = $q;
        return view('modules.callus_transfer.print_label_layout',compact('data'));
    }
    public function printByTransfer(Request $request){
        $data['title'] = "Print Label Sub Cultur 1";
        $data['desc'] = "Print Label By Date Group";
        $q = TcCallusTransferBottle::where('tc_callus_transfer_id',$request->transferId)
            ->orderBy('bottle_number','asc')
            ->get();

        $data['bottles'] = $q;
        return view('modules.callus_transfer.print_label_layout',compact('data'));
    }

    public function destroy($id)
    {
        $q = TcEmbryoBottle::where('tc_callus_transfer_id',$id)->get();
        foreach ($q as $key => $value) {
            $aryId[] = $value->id;
        }
        $q = TcEmbryoObDetail::whereIn('tc_embryo_bottle_id',$aryId)->get()->count();
        if($q==0){
            TcCallusTransfer::where('id',$id)->forceDelete();
            TcCallusTransferBottle::where('tc_callus_transfer_id',$id)->forceDelete();
            TcCallusTransferStock::where('tc_callus_transfer_id',$id)->forceDelete();
            TcEmbryoBottle::where('tc_callus_transfer_id',$id)->forceDelete();
            return alert(1,null,null);
        }else{
            return alert(0,'Failed, related data is still in use.',null);
        }
    }

}
