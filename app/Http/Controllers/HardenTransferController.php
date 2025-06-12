<?php

namespace App\Http\Controllers;

use App\Models\TcHarden;
use App\Models\TcHardenOb;
use App\Models\TcHardenObDetail;
use App\Models\TcHardenTransfer;
use App\Models\TcHardenTree;
use App\Models\TcInit;
use App\Models\TcNur;
use App\Models\TcNurObDetail;
use App\Models\TcNurTree;
use App\Models\TcSample;
use App\Models\TcWorker;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HardenTransferController extends Controller
{
    public function index()
    {
        $data['title'] = "Hardening Transfer (View Per Sample)";
        $data['desc'] = "Display all available sample to transfer";
        return view('modules.harden_transfer.index', compact('data'));
    }
    public function dtIndex(Request $request)
    {
        $data = TcInit::select('tc_inits.id','tc_inits.tc_sample_id')
            ->with([
                'tc_samples:id,program,sample_number'
            ])
            ->whereHas('tc_harden_ob_details')
            ->withCount('tc_harden_transfers as transfer_count')
            ->withCount(['tc_harden_ob_details as transferred' => function($q){
                $q->where('is_transfer', 1)
                    ->where('status',1);
            }])
            ->withCount(['tc_harden_ob_details as need_transfer' => function($q){
                $q->where('is_transfer', 1)
                    ->where('status',0);
            }])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->editColumn('sample_number_format', function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('harden-transfers.show',$data->id)."'>View</a>
                ";
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Hardening Transfer";
        $data['desc'] = "Create new transfer harden.";
        $data['transferCount'] = TcHardenTransfer::select('id')->where('tc_init_id',$id)->get()->count();
        $q = collect(TcHardenObDetail::where('tc_init_id',$id)->get()->toArray());
        $data['transferred'] = $q->where('status',1)->sum('is_transfer');
        $data['need_transfer'] = $q->where('status',0)->sum('is_transfer');
        $data['initId'] = $id;
        $q = TcSample::select('id','sample_number')
            ->whereHas('tc_inits', function($q) use($id){
                $q->where('id',$id);
            })->first();
        $data['sampleNumber'] = $q->sample_number_display;
        $data['worker'] = TcWorker::select('id','code')->get();
        return view('modules.harden_transfer.show', compact('data'));
    }

    public function dtShow(Request $request)
    {
        $data = TcHardenOb::select([
                'tc_harden_obs.*',
                DB::raw('convert(varchar,tc_hardens.tree_date, 103) as tree_date_format'),
                DB::raw('convert(varchar,ob_date, 103) as ob_date_format'),
            ])
            ->where('tc_harden_obs.tc_init_id', $request->initId)
            ->leftJoin('tc_hardens','tc_hardens.id','=','tc_harden_obs.tc_harden_id')
            ->whereHas('tc_harden_ob_details',function($q){
                $q->where('status',0);
            })
            ->withCount(['tc_harden_ob_details as need_transfer' => function($q){
                $q->where('status',0)->where('is_transfer',1);
            }])
            ->with([
                'tc_workers'
            ])
        ;

        return DataTables::of($data)
            ->filterColumn('tree_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,tc_hardens.tree_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->filterColumn('ob_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,ob_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('button_transfer',function($data){
                $val = TcHardenObDetail::where('tc_harden_ob_id',$data->id)->where('status',0)->where('is_transfer',1)->get()->count();
                return '<a href="#transferModal" data-toggle="modal" data-target="#transferModal" class="btn btn-sm btn-primary py-0" data-id="'.$data->id.'" data-val="'.$val.'" data-hardendate="'.$data->tree_date_format.'" data-obsdate="'.$data->ob_date_format.'">Transfer</a>';
            })
            ->rawColumns(['button_transfer'])
            ->smart(false)->toJson();
    }

    public function dtShow2(Request $request)
    {
        $data = TcHardenTransfer::select([
                'tc_harden_transfers.*',
                DB::raw('convert(varchar,transfer_date, 103) as transfer_date_format'),
                DB::raw('convert(varchar,tc_harden_obs.ob_date, 103) as ob_date_format'),
            ])
            ->leftJoin('tc_harden_obs','tc_harden_obs.id','tc_harden_transfers.tc_harden_ob_id')
            ->with([
                'tc_workers'
            ])
        ;

        return DataTables::of($data)
            ->filterColumn('ob_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,transfer_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->filterColumn('transfer_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,tc-harden_obs.ob_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('btn_delete',function($data){
                $q = TcHardenTransfer::where('id',$data->id)->first();
                $self = $next = true;
                if($q->to_next != 0){
                    $q2 = TcNur::where('tc_harden_transfer_id',$data->id)->first();
                    $q2Id = $q2->id;
                    $cek = TcNurObDetail::select('id')->whereHas('tc_nur_trees',function($param) use($q2Id){
                        $param->where('tc_nur_id',$q2Id);
                    })->get()->count();
                    $next = $cek == 0?true:false;
                }

                if($q->to_self != 0){
                    $q3 = TcHarden::where('tc_harden_transfer_id',$data->id)->first();
                    $q3Id = $q3->id;
                    $cek = TcHardenObDetail::select('id')->whereHas('tc_harden_trees',function($param) use($q3Id){
                        $param->where('tc_harden_id',$q3Id);
                    })->get()->count();
                    $self = $cek == 0?true:false;
                }

                $el = '<div class="btn-group btn-group-sm">';
                if($data->to_self != 0){
                    $el .= '
                        <button type="button" class="btn btn-primary py-0 printLabel" data-type="self" transfer-id="'.$data->id.'">Print Harden</button>
                    ';
                }
                if($self && $next){
                    $el .= '<a href="#delModal" data-toggle="modal" data-target="#delModal" class="btn btn-sm btn-danger py-0" data-id="'.$data->id.'" data-attr="'.$data->transfer_date_format.'">Delete</a>';
                }
                if($data->to_next != 0){
                    $el .= '
                        <button type="button" class="btn btn-success py-0 printLabel" data-type="next" transfer-id="'.$data->id.'">Print Nursery</button>
                    ';
                }
                $el .= '</div>';
                return $el;
            })
            ->rawColumns(['btn_delete'])
            ->smart(false)->toJson();
    }

    public function store(Request $request)
    {
        if($request->max != ($request->to_self + $request->to_next)){
            return alert(0,'Total transfer must be '.$request->max,'alert-area-modal-transfer');
        }else{

            $dtObs = TcHardenOb::where('id',$request->tc_harden_ob_id)->first();
            $sub = $dtObs->tc_hardens->sub;
            $type = $dtObs->tc_hardens->type;
            $alpha = $dtObs->tc_hardens->alpha;
            $initId = $dtObs->tc_init_id;

            // ke table harden_transfers
            $dt = [
                'tc_init_id' => $initId,
                'tc_harden_ob_id' => $request->tc_harden_ob_id,
                'tc_worker_id' => $request->tc_worker_id,
                'transfer_date' => $request->transfer_date,
                'to_self' => $request->to_self,
                'to_next' => $request->to_next,
            ];

            $q = TcHardenTransfer::create($dt);
            $transId = $q->id;
            unset($dt);

            if($request->to_self !=0){
                $dt = [
                    'tc_init_id' => $initId,
                    'tc_harden_transfer_id' => $transId,
                    'tc_worker_id' => $request->tc_worker_id,
                    'sub' => $sub,
                    'type' =>$type,
                    'alpha' => $alpha,
                    'tree_date' => $request->transfer_date,
                ];
                $q = TcHarden::create($dt);
                unset($dt);
                $hardenId = $q->id;

                for ($i=1; $i <= $request->to_self ; $i++) {
                    $dtUse[] = [
                        'tc_init_id' => $initId,
                        'tc_harden_id' => $hardenId,
                        'index_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                TcHardenTree::insert($dtUse);
                unset($dtUse);
            }

            if($request->to_next !=0){
                $dt = [
                    'tc_init_id' => $initId,
                    'tc_harden_transfer_id' => $transId,
                    'tc_worker_id' => $request->tc_worker_id,
                    'sub' => $sub,
                    'type' =>$type,
                    'alpha' => $alpha,
                    'tree_date' => $request->transfer_date,
                ];
                $q = TcNur::create($dt);
                $nurId = $q->id;

                for ($i=1; $i <= $request->to_next ; $i++) {
                    $dtUse[] = [
                        'tc_init_id' => $initId,
                        'tc_nur_id' => $nurId,
                        'index_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                TcNurTree::insert($dtUse);
            }

            TcHardenObDetail::where('tc_harden_ob_id',$request->tc_harden_ob_id)->update(['status' => 1]);
            TcHarden::statusHarden($dtObs->tc_harden_id);

            return alert(1,null,null);
        }
    }

    public function destroy($id)
    {
        $q = TcHardenTransfer::where('id',$id)->first();
        $obsId = $q->tc_harden_ob_id;
        $self = $next = true;
        if($q->to_next != 0){
            $q2 = TcNur::where('tc_harden_transfer_id',$id)->first();
            $q2Id = $q2->id;
            $cek = TcNurObDetail::select('id')->whereHas('tc_nur_trees',function($param) use($q2Id){
                $param->where('tc_nur_id',$q2Id);
            })->get()->count();
            $next = $cek == 0?true:false;
        }

        if($q->to_self != 0){
            $q3 = TcHarden::where('tc_harden_transfer_id',$id)->first();
            $q3Id = $q3->id;
            $cek = TcHardenObDetail::select('id')->whereHas('tc_harden_trees',function($param) use($q3Id){
                $param->where('tc_harden_id',$q3Id);
            })->get()->count();
            $self = $cek == 0?true:false;
        }

        if($self && $next){
            // hapus di nur
            if($q->to_next != 0){
                $q2->forceDelete();
                TcNurTree::where('tc_nur_id',$q2Id)->forceDelete();
            }

            // hapus dan update di harden
            if($q->to_self != 0){
                $q3->forceDelete();
                TcHardenTree::where('tc_harden_id',$q3Id)->forceDelete();
            }
            TcHardenObDetail::where('tc_harden_ob_id',$obsId)->update(['status' => 0]);
            TcHardenTransfer::where('id',$id)->forceDelete();
            TcHarden::statusHarden($q->tc_harden_obs->tc_harden_id);
            return alert(1,null,'alert-area2');
        }else{
            return alert(0,null,'alert-area2');
        }


    }

    public function printLabel(Request $request)
    {
        $data['title'] = "Print Label";
        $data['desc'] = "Print Label";

        if($request->type == 'self'){
            $data['transfer'] = TcHardenTree::whereHas('tc_hardens',function($q) use($request){
                    $q->where('tc_harden_transfer_id',$request->id);
                })
                ->with([
                    'tc_hardens',
                    'tc_hardens.tc_inits' => function($q){
                        $q->select('id','tc_sample_id');
                    },
                    'tc_hardens.tc_inits.tc_samples' => function($q){
                        $q->select('id','sample_number');
                    },
                    'tc_hardens.tc_workers' => function($q){
                        $q->select('id','code');
                    }
                ])->get()->toArray();
            return view('modules.harden_transfer.print_label_layout',compact('data'));
        }else{
            $data['transfer'] = TcNurTree::whereHas('tc_nurs',function($q) use($request){
                    $q->where('tc_harden_transfer_id',$request->id);
                })
                ->with([
                    'tc_nurs',
                    'tc_nurs.tc_inits' => function($q){
                        $q->select('id','tc_sample_id');
                    },
                    'tc_nurs.tc_inits.tc_samples' => function($q){
                        $q->select('id','sample_number');
                    },
                    'tc_nurs.tc_workers' => function($q){
                        $q->select('id','code');
                    }
                ])->get()->toArray();
            return view('modules.harden_transfer.print_label_layout2',compact('data'));
        }

    }
}
