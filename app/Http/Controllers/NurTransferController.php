<?php

namespace App\Http\Controllers;

use App\Models\TcNur;
use App\Models\TcNurOb;
use App\Models\TcNurObDetail;
use App\Models\TcNurTransfer;
use App\Models\TcNurTree;
use App\Models\TcInit;
use App\Models\TcField;
use App\Models\TcFieldObDetail;
use App\Models\TcFieldTree;
use App\Models\TcPlantation;
use App\Models\TcSample;
use App\Models\TcWorker;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class NurTransferController extends Controller
{
    public function index()
    {
        $data['title'] = "Nursery Transfer (View Per Sample)";
        $data['desc'] = "Display all available sample to transfer";
        return view('modules.nur_transfer.index', compact('data'));
    }
    public function dtIndex(Request $request)
    {
        $data = TcInit::select('tc_inits.id','tc_inits.tc_sample_id')
            ->with([
                'tc_samples:id,program,sample_number'
            ])
            ->whereHas('tc_nur_ob_details')
            ->withCount('tc_nur_transfers as transfer_count')
            ->withCount(['tc_nur_ob_details as transferred' => function($q){
                $q->where('is_transfer', 1)
                    ->where('status',1);
            }])
            ->withCount(['tc_nur_ob_details as need_transfer_nursery' => function($q){
                $q->where('status',0)->where('is_transfer', 1)->whereHas('tc_nur_obs',function($q2){
                    $q2->whereHas('tc_nurs',function($q3){
                        $q3->where('category',1);
                    });
                });
            }])
            ->withCount(['tc_nur_ob_details as need_transfer_estate' => function($q){
                $q->where('status',0)->where('is_transfer', 1)->whereHas('tc_nur_obs',function($q2){
                    $q2->whereHas('tc_nurs',function($q3){
                        $q3->where('category',2);
                    });
                });
            }])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->editColumn('sample_number_format', function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('nur-transfers.show',$data->id)."'>View</a>
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
        $data['title'] = "Nursery Transfer";
        $data['desc'] = "Create new transfer nur.";
        $data['transferCount'] = TcNurTransfer::select('id')->where('tc_init_id',$id)->get()->count();
        $q = collect(TcNurObDetail::where('tc_init_id',$id)->get()->toArray());
        $data['transferred'] = $q->where('status',1)->sum('is_transfer');
        $data['need_transfer'] = $q->where('status',0)->sum('is_transfer');
        $data['initId'] = $id;
        $q = TcSample::select('id','sample_number')
            ->whereHas('tc_inits', function($q) use($id){
                $q->where('id',$id);
            })->first();
        $data['sampleNumber'] = $q->sample_number_display;
        $data['worker'] = TcWorker::select('id','code')->get();
        $data['plant'] = TcPlantation::all();
        return view('modules.nur_transfer.show', compact('data'));
    }

    public function dtShow(Request $request)
    {
        $data = TcNurOb::select([
                'tc_nur_obs.*',
                DB::raw('convert(varchar,tc_nurs.tree_date, 103) as tree_date_format'),
                DB::raw('convert(varchar,ob_date, 103) as ob_date_format'),
                DB::raw("IIF(tc_nurs.category=1, 'Nursery', 'Estate') as cat"),
            ])
            ->where('tc_nur_obs.tc_init_id', $request->initId)
            ->leftJoin('tc_nurs','tc_nurs.id','=','tc_nur_obs.tc_nur_id')
            ->whereHas('tc_nur_ob_details',function($q){
                $q->where('status',0);
            })
            ->withCount(['tc_nur_ob_details as need_transfer' => function($q){
                $q->where('status',0)->where('is_transfer',1);
            }])
            ->with([
                'tc_workers'
            ])
        ;

        return DataTables::of($data)
            ->filterColumn('tree_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,tc_nurs.tree_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->filterColumn('ob_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,ob_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('button_transfer',function($data){
                $val = TcNurObDetail::where('tc_nur_ob_id',$data->id)->where('status',0)->where('is_transfer',1)->get()->count();
                return '<a href="#transferModal" data-toggle="modal" data-target="#transferModal" class="btn btn-sm btn-primary py-0" data-id="'.$data->id.'" data-val="'.$val.'" data-nurdate="'.$data->tree_date_format.'" data-obsdate="'.$data->ob_date_format.'">Transfer</a>';
            })
            ->filterColumn('cat', function($query, $keyword) {
                $sql = "IIF(tc.nurs.category=1, 'Nursery', 'Estate') like ?";
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('cat_format',function($data){
                if($data->tc_nurs->category == 1){
                    return '<span class="badge badge-primary">NURSERY</span>';
                }else{
                    return '<span class="badge badge-success">ESTATE</span>';
                }
            })
            ->rawColumns(['button_transfer','cat_format'])
            ->smart(false)->toJson();
    }

    public function dtShow2(Request $request)
    {
        $data = TcNurTransfer::select([
                'tc_nur_transfers.*',
                DB::raw('convert(varchar,transfer_date, 103) as transfer_date_format'),
                DB::raw('convert(varchar,tc_nur_obs.ob_date, 103) as ob_date_format'),
            ])
            ->leftJoin('tc_nur_obs','tc_nur_obs.id','tc_nur_transfers.tc_nur_ob_id')
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
                $sql = 'convert(varchar,tc-nur_obs.ob_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('btn_delete',function($data){
                $q = TcNurTransfer::where('id',$data->id)->first();
                $self = $next = true;
                if($q->to_next != 0){
                    $q2 = TcField::where('tc_nur_transfer_id',$data->id)->first();
                    $q2Id = $q2->id;
                    $cek = TcFieldObDetail::select('id')->whereHas('tc_field_trees',function($param) use($q2Id){
                        $param->where('tc_field_id',$q2Id);
                    })->get()->count();
                    $next = $cek == 0?true:false;
                }

                if($q->to_self != 0){
                    $q3 = TcNur::where('tc_nur_transfer_id',$data->id)->first();
                    $q3Id = $q3->id;
                    $cek = TcNurObDetail::select('id')->whereHas('tc_nur_trees',function($param) use($q3Id){
                        $param->where('tc_nur_id',$q3Id);
                    })->get()->count();
                    $self = $cek == 0?true:false;
                }

                $el = '<div class="btn-group btn-group-sm">';
                if($data->to_self != 0){
                    $el .= '
                        <button type="button" class="btn btn-primary py-0 printLabel" data-type="self" transfer-id="'.$data->id.'"><i class="fas fa-print"></i> Nursery</button>
                    ';
                }
                if($data->to_self2 != 0){
                    $el .= '
                        <button type="button" class="btn btn-primary py-0 printLabel" data-type="self2" transfer-id="'.$data->id.'"><i class="fas fa-print"></i> Estate</button>
                    ';
                }
                if($data->to_next != 0){
                    $el .= '
                        <button type="button" class="btn btn-success py-0 printLabel" data-type="next" transfer-id="'.$data->id.'"><i class="fas fa-print"></i> Field</button>
                    ';
                }
                if($self && $next){
                    $el .= '<a href="#delModal" data-toggle="modal" data-target="#delModal" class="btn btn-sm btn-danger py-0" data-id="'.$data->id.'" data-attr="'.$data->transfer_date_format.'">Delete</a>';
                }

                $el .= '</div>';
                return $el;
            })
            ->rawColumns(['btn_delete'])
            ->smart(false)->toJson();
    }

    public function store(Request $request)
    {
        if($request->max != ($request->to_self + $request->to_self2 + $request->to_next)){
            return alert(0,'Total transfer must be '.$request->max,'alert-area-modal-transfer');
        }else{

            $dtObs = TcNurOb::where('id',$request->tc_nur_ob_id)->first();
            $sub = $dtObs->tc_nurs->sub;
            $type = $dtObs->tc_nurs->type;
            $alpha = $dtObs->tc_nurs->alpha;
            $initId = $dtObs->tc_init_id;

            // ke table nur_transfers
            $dt = [
                'tc_init_id' => $initId,
                'tc_nur_ob_id' => $request->tc_nur_ob_id,
                'tc_worker_id' => $request->tc_worker_id,
                'transfer_date' => $request->transfer_date,
                'to_self' => $request->to_self,
                'to_self2' => $request->to_self2,
                'to_next' => $request->to_next,
            ];

            $q = TcNurTransfer::create($dt);
            $transId = $q->id;
            unset($dt);

            if($request->to_self !=0){
                $dt = [
                    'tc_init_id' => $initId,
                    'tc_nur_transfer_id' => $transId,
                    'tc_worker_id' => $request->tc_worker_id,
                    'sub' => $sub,
                    'type' =>$type,
                    'alpha' => $alpha,
                    'tree_date' => $request->transfer_date,
                ];
                $q = TcNur::create($dt);
                unset($dt);
                $nurId = $q->id;

                for ($i=1; $i <= $request->to_self ; $i++) {
                    $dtUse[] = [
                        'tc_init_id' => $initId,
                        'tc_nur_id' => $nurId,
                        'index_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                TcNurTree::insert($dtUse);
                unset($dtUse);
            }

            if($request->to_self2 !=0){
                $dt = [
                    'tc_init_id' => $initId,
                    'tc_nur_transfer_id' => $transId,
                    'tc_worker_id' => $request->tc_worker_id,
                    'category' => 2,
                    'block' => $request->block_es,
                    'row' => $request->row_es,
                    'tree' => $request->tree_es,
                    'tc_plantation_id' => $request->plant_es,
                    'sub' => $sub,
                    'type' =>$type,
                    'alpha' => $alpha,
                    'tree_date' => $request->transfer_date,
                ];
                $q = TcNur::create($dt);
                unset($dt);
                $nurId = $q->id;

                for ($i=1; $i <= $request->to_self2 ; $i++) {
                    $dtUse[] = [
                        'tc_init_id' => $initId,
                        'tc_nur_id' => $nurId,
                        'index_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                TcNurTree::insert($dtUse);
                unset($dtUse);
            }

            if($request->to_next !=0){
                $dt = [
                    'tc_init_id' => $initId,
                    'tc_nur_transfer_id' => $transId,
                    'tc_worker_id' => $request->tc_worker_id,
                    'block' => $request->block_f,
                    'row' => $request->row_f,
                    'tree' => $request->tree_f,
                    'tc_plantation_id' => $request->plant_f,
                    'sub' => $sub,
                    'type' =>$type,
                    'alpha' => $alpha,
                    'tree_date' => $request->transfer_date,
                ];
                $q = TcField::create($dt);
                $fieldId = $q->id;

                for ($i=1; $i <= $request->to_next ; $i++) {
                    $dtUse[] = [
                        'tc_init_id' => $initId,
                        'tc_field_id' => $fieldId,
                        'index_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                TcFieldTree::insert($dtUse);
            }

            TcNurObDetail::where('tc_nur_ob_id',$request->tc_nur_ob_id)->update(['status' => 1]);
            TcNur::statusNur($dtObs->tc_nur_id);

            return alert(1,null,null);
        }
    }

    public function destroy($id)
    {
        $q = TcNurTransfer::where('id',$id)->first();
        $obsId = $q->tc_nur_ob_id;
        $self = $self2 = $next = true;

        if($q->to_next != 0){
            $q2 = TcField::where('tc_nur_transfer_id',$id)->first();
            $q2Id = $q2->id;
            $cek = TcFieldObDetail::select('id')->whereHas('tc_field_trees',function($param) use($q2Id){
                $param->where('tc_field_id',$q2Id);
            })->get()->count();
            $next = $cek == 0?true:false;
        }

        if($q->to_self != 0){
            $q3 = TcNur::where('tc_nur_transfer_id',$id)->where('category',1)->first();
            $q3Id = $q3->id;
            $cek = TcNurObDetail::select('id')->whereHas('tc_nur_trees',function($param) use($q3Id){
                $param->where('tc_nur_id',$q3Id);
            })->get()->count();
            $self = $cek == 0?true:false;
        }

        if($q->to_self2 !=0){
            $q4 = TcNur::where('tc_nur_transfer_id',$id)->where('category',2)->first();
            $q4Id = $q4->id;
            $cek = TcNurObDetail::select('id')->whereHas('tc_nur_trees',function($param) use($q4Id){
                $param->where('tc_nur_id',$q4Id);
            })->get()->count();
            $self2 = $cek == 0?true:false;
        }

        if($self && $self2 && $next){
            // hapus di field
            if($q->to_next != 0){
                $q2->forceDelete();
                TcFieldTree::where('tc_field_id',$q2Id)->forceDelete();
            }

            // hapus dan update di nur
            if($q->to_self != 0){
                $q3->forceDelete();
                TcNurTree::where('tc_nur_id',$q3Id)->forceDelete();
            }

            // hapus dan update di nur
            if($q->to_self2 != 0){
                $q4->forceDelete();
                TcNurTree::where('tc_nur_id',$q4Id)->forceDelete();
            }

            TcNurObDetail::where('tc_nur_ob_id',$obsId)->update(['status' => 0]);
            TcNurTransfer::where('id',$id)->forceDelete();
            TcNur::statusNur($q->tc_nur_obs->tc_nur_id);
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
            $data['transfer'] = TcNurTree::whereHas('tc_nurs',function($q) use($request){
                    $q->where('tc_nur_transfer_id',$request->id)->where('category',1);
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
            return view('modules.nur_transfer.print_label_layout',compact('data'));
        }else if($request->type == 'self2'){
            $data['transfer'] = TcNurTree::whereHas('tc_nurs',function($q) use($request){
                $q->where('tc_nur_transfer_id',$request->id)->where('category',2);
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
            return view('modules.nur_transfer.print_label_layout',compact('data'));
        }else{
            $data['transfer'] = TcFieldTree::whereHas('tc_fields',function($q) use($request){
                    $q->where('tc_nur_transfer_id',$request->id);
                })
                ->with([
                    'tc_fields',
                    'tc_fields.tc_inits' => function($q){
                        $q->select('id','tc_sample_id');
                    },
                    'tc_fields.tc_inits.tc_samples' => function($q){
                        $q->select('id','sample_number');
                    },
                    'tc_fields.tc_workers' => function($q){
                        $q->select('id','code');
                    }
                ])->get()->toArray();
            return view('modules.nur_transfer.print_label_layout2',compact('data'));
        }

    }
}
