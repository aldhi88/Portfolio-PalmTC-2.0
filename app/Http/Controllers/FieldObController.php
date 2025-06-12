<?php

namespace App\Http\Controllers;

use App\Models\TcDeath;
use App\Models\TcField;
use App\Models\TcFieldOb;
use App\Models\TcFieldObDetail;
use App\Models\TcFieldTree;
use App\Models\TcInit;
use App\Models\TcSample;
use App\Models\TcWorker;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FieldObController extends Controller
{
    public function index()
    {
        $data['title'] = "Field (Per Sample)";
        $data['desc'] = "Display all observation summary by sample data";
        $data['death'] = TcDeath::all();
        return view('modules.field_ob.index',compact('data'));
    }
    public function dt(Request $request)
    {
        $data = TcInit::select([
                'tc_inits.id',
                'tc_inits.tc_sample_id',
            ])
            ->with([
                'tc_samples:sample_number,id,program'
            ])
            ->whereHas('tc_fields')
            ->withCount(['tc_fields as field_count' => function($q){
                $q->where('tc_fields.status',1);
            }])
            ->withCount(['tc_field_obs as obs_count' => function($q){
                $q->where('tc_field_obs.status', 1);
            }])
            ->withCount([
                'tc_field_trees as total_data' => function($q){
                    $q->where('tc_field_trees.status','!=',0);
                }
            ])
            ->withCount([
                'tc_field_ob_details as total_death' => function($q){
                    $q->where('is_death',1)
                       ->whereHas('tc_field_trees', function($q2){
                           $q2->whereHas('tc_fields',function($q3){
                               $q3->where('status',1);
                           });
                       });
                }
            ])
            ->withCount(['tc_field_trees as field_active' => function($q){
                $q->where('tc_field_trees.status', 1);
            }])
        ;
        $dtDeath = TcDeath::all();
        foreach ($dtDeath as $key => $value) {
            $deathId = $value->id;
            $data->withCount([
                'tc_field_ob_details as total_death_'.$value->id => function($q) use($deathId){
                    $q->where('is_death',1)->where('tc_death_id',$deathId)
                       ->whereHas('tc_field_trees', function($q2){
                           $q2->whereHas('tc_fields',function($q3){
                               $q3->where('status',1);
                           });
                       })
                    ;
                }
            ]);
        }   

        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('field-obs.show',$data->id).'">Detail</a>
                ';
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Field Observation Data";
        $data['desc'] = "Display all field trees list";
        $data['initId'] = $id;
        $data['sampleNumber'] = TcSample::select('id','sample_number')
            ->whereHas('tc_inits', function(Builder $q) use($id){
                $q->where('id',$id);
            })
            ->first()->getAttribute('sample_number_display');
        $data['death'] = TcDeath::all();
        return view('modules.field_ob.show',compact('data'));
    }

    public function dtShow(Request $request)
    {
        $data = TcField::select([
                'tc_fields.*',
                DB::raw('convert(varchar,tree_date, 103) as tree_date_format'),
            ])
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits:id,tc_sample_id',
                'tc_inits.tc_samples' => function($q){
                    $q->select('id','program','sample_number');
                },
                'tc_workers:id,code',
            ])
            ->withCount([
                'tc_field_trees as total_data' => function($q){
                    $q->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_field_obs as total_obs' => function($q){
                    $q->where('status',1);
                }
            ])
            ->withCount([
                'tc_field_ob_details as total_death' => function($q){
                    $q->where('is_death',1);
                }
            ])
            ->withCount([
                'tc_field_ob_details as normal' => function($q){
                    $q->where('is_normal',1);
                }
            ])
            ->withCount([
                'tc_field_ob_details as abnormal' => function($q){
                    $q->where('is_normal',2);
                }
            ])
            ->withCount([
                'tc_field_ob_details as panen' => function($q){
                    $q->select(DB::raw('sum(tc_field_ob_details.load)'));
                }
            ])
        ;
        if($request->filter == 1 || !isset($request->filter)){
            $data->where('tc_fields.status','!=',0);
        }
        
        $dtDeath = TcDeath::all();
        foreach ($dtDeath as $key => $value) {
            $deathId = $value->id;
            $data->withCount([
                'tc_field_ob_details as total_death_'.$value->id => function($q) use($deathId){
                    $q->where('is_death',1)->where('tc_death_id',$deathId);
                }
            ]);
        }   

        $initId = $request->initId;
        return DataTables::of($data)
            ->filterColumn('tree_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,tree_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('tree_date_action',function($data) use($initId){
                $el = '<p class="mb-0"><strong>'.$data->tree_date_format.'</strong></p>';
                $q = TcFieldOb::where('tc_field_id',$data->id)->where('status',0)->get();
                if($q->count() == 0){
                    $dt['tc_init_id'] = $initId;
                    $dt['tc_field_id'] = $data->id;
                    $q = TcFieldOb::create($dt);unset($dt);
                    $obId = $q->id;
                }else{
                    $obId = $q->first()->id;
                }   

                if($data->total_active != 0){
                    $el .= '
                        <a class="text-primary detail fs-13" data-date="'.$data->tree_date_format.'" data-id="'.$data->id.'" href="'.route('field-obs.create',$obId).'">Obs</a> - 
                    ';
                }

                $el .= '
                    <a class="text-primary detail fs-13" data-date="'.$data->tree_date_format.'" data-id="'.$data->id.'" href="#'.$data->id.'">Detail</a>
                ';
                return $el;
            })
            ->rawColumns(['tree_date_action'])
            ->smart(false)->toJson();
    }

    public function dtShow2(Request $request)
    {
        $fieldId = $request->filter;

        $data = TcFieldOb::select([
                'tc_field_obs.*',
                DB::raw('convert(varchar,ob_date, 103) as ob_date_format'),
            ])
            ->where('tc_field_id',$fieldId)
            ->where('status',1)
            ->with([
                'tc_fields'
            ])
            ->withCount([
                'tc_field_ob_details as total_death' => function($q){
                    $q->where('is_death',1);
                }
            ])
            ->withCount([
                'tc_field_ob_details as normal' => function($q){
                    $q->where('is_normal',1);
                }
            ])
            ->withCount([
                'tc_field_ob_details as abnormal' => function($q){
                    $q->where('is_normal',2);
                }
            ])
            ->withCount([
                'tc_field_ob_details as panen' => function($q){
                    $q->select(DB::raw('sum(tc_field_ob_details.load)'));
                }
            ])
        ;

        $dtDeath = TcDeath::all();
        foreach ($dtDeath as $key => $value) {
            $deathId = $value->id;
            $data->withCount([
                'tc_field_ob_details as total_death_'.$value->id => function($q) use($deathId){
                    $q->where('is_death',1)->where('tc_death_id',$deathId);
                }
            ]);
        } 
        return DataTables::of($data)
            ->addColumn('tree_date_format',function($data){
                return Carbon::parse($data->tc_fields->tree_date)->format('d/m/Y');
            })
            ->filterColumn('ob_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,ob_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('ob_date_action',function($data){
                $el = '<p class="mb-0"><strong>'.$data->ob_date_format.'</strong></p>';
                $el .= '
                    <a class="text-primary detail fs-13" href="'.route('field-obs.create',$data->id).'">Edit</a>
                ';
                $q = TcFieldObDetail::where('tc_field_ob_id',$data->id)->get()->count();
                if($q==0){
                    $el .= " - <a class='text-danger fs-13' href='#delModal' data-toggle='modal' data-target='#delModal' data-attr='".Carbon::parse($data->ob_date)->format('d/m/Y')."' data-id='".$data->id."'>Delete</a>";
                }
                return $el;
            })
            ->rawColumns(['ob_date_action'])
            ->smart(false)->toJson();
    }

    public function create($obsId)
    {
        $data['title'] = "Field Observation";
        $data['desc'] = "Display acclimatization observation form";
        $data['workers'] = TcWorker::where('status',1)->get();
        $qObs = TcFieldOb::where('id',$obsId)->with('tc_inits')->first();
        $data['initId'] = $qObs->tc_init_id;
        $data['sample'] = $qObs->tc_inits->tc_samples->sample_number_display;
        $data['worker_now'] = $qObs->tc_worker_id;
        $data['date_ob'] = is_null($qObs->ob_date)?false:Carbon::parse($qObs->ob_date)->format('Y-m-d');
        $data['obsId'] = $obsId;
        $data['start'] = $qObs->status==0?false:true;
        $data['tc_field_id'] = $qObs->tc_field_id;

        $collect = collect(TcFieldObDetail::where('tc_field_ob_id',$qObs->id)->get()->toArray());
        $treeCount = TcFieldTree::where('status','!=',0)->where('tc_field_id',$data['tc_field_id'])->get()->count();
        $data['isCheck'] = null;
        if($treeCount == $collect->count()){
            if($collect->where('is_death',1)->count() == 0){
                $data['isCheck'] = 'checked';
            }
        }
        return view('modules.field_ob.create',compact('data'));
    }

    public function store(Request $request)
    {
        $data['tc_worker_id'] = $request->tc_worker_id;
        $data['ob_date'] = $request->date_ob;
        $data['status'] = 1;
        TcFieldOb::where('id',$request->id)->update($data);
        $q = TcFieldOb::where('tc_field_id',$request->tc_field_id)->where('status',0)->get()->count();
        if($q == 0){
            TcFieldOb::create([
                'tc_init_id' => $request->tc_init_id,
                'tc_field_id' => $request->tc_field_id
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, observation data has been created.',
                'status' => 'update',
                'tc_worker_id' => $request->tc_worker_id,
                'obs_date' => $request->date_ob,
            ],
        ]);
    }

    public function dtObs(Request $request)
    {
        $fieldId = $request->fieldId;
        $obsId = $request->obsId;
        $data = TcFieldTree::select([
                'tc_field_trees.*',
                DB::raw('convert(varchar,tc_fields.tree_date, 103) as tree_date_format'),
            ])
            ->where('tc_field_id',$fieldId)
            ->where('tc_field_trees.status','!=',0)
            ->whereDoesntHave('tc_field_ob_details', function(Builder $q) use($obsId){
                $q->where('tc_field_ob_id','!=',$obsId)->where('is_death',1);
            })
            ->leftJoin('tc_fields','tc_fields.id','=','tc_field_trees.tc_field_id');
        return DataTables::of($data)
            ->addColumn('death_form',function($data) use($obsId){
                $deathId = null;
                $cek = TcFieldObDetail::where('tc_field_tree_id',$data->id)
                    ->where('tc_field_ob_id',$obsId)->get();
                if(count($cek)!=0 && $cek->first()->is_death == 1){
                    $deathId = $cek->first()->tc_death_id;
                }
                $dtDeath = TcDeath::all();
                $el = '<select name="death" class="death-pick w-100 text-center" data-id="'.$data->id.'">';
                $el .= '<option value="0">- Choose -</option>';
                foreach ($dtDeath as $key => $value) {
                    $check = $deathId == $value->id?'selected':null;
                    $el .= '<option '.$check.' value="'.$value->id.'">'.$value->code.'</option>';
                }
                $el .= '</select>';
                return $el;
            })
            ->addColumn('normal_form',function($data) use($obsId){
                $cek = TcFieldObDetail::where('tc_field_tree_id',$data->id)
                    ->where('tc_field_ob_id',$obsId)->get();
                $isNormal = null;
                if(count($cek)!=0){
                    $isNormal = $cek->first()->is_normal;
                }
                $check0 = $isNormal==0?'selected':null;
                $check1 = $isNormal==1?'selected':null;
                $check2 = $isNormal==2?'selected':null;
                $el = '<select name="normal" class="normal-pick w-100 text-center" data-id="'.$data->id.'">';
                $el .= '<option '.$check0.' value="0">- Choose -</option>';
                $el .= '<option '.$check1.' value="1">Normal</option>';
                $el .= '<option '.$check2.' value="2">Abnormal</option>';
                $el .= '</select>';
                return $el;
            })
            ->addColumn('production_form',function($data) use($obsId){
                $cek = TcFieldObDetail::where('tc_field_tree_id',$data->id)
                    ->where('tc_field_ob_id',$obsId)->get();
                $load = null;
                $readonly = 'readonly';
                if(count($cek)!=0){
                    $load = $cek->first()->load;
                    $readonly = ($cek->first()->is_normal == 0)?'readonly':null;
                }
                $el = '<input placeholder="'.$load.'" '.$readonly.' name="load" type="number" class="pro w-100 text-center" data-id="'.$data->id.'" >';
                return $el;
            })
            ->rawColumns(['status_format','death_form','normal_form','production_form'])
            ->smart(false)->toJson();
    }

    public function storeDetail(Request $request)
    {
        if($request->action == 'death'){
            TcFieldTree::where('id',$request->tc_field_tree_id)->update(['status'=>2]);
            if($request->is_death == 1){
                $q = TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                    ->where('tc_field_tree_id',$request->tc_field_tree_id)->get();
                if(count($q)==0){
                    $data = [
                        'tc_init_id' => $request->tc_init_id,
                        'tc_field_tree_id' => $request->tc_field_tree_id,
                        'tc_field_ob_id' => $request->tc_field_ob_id,
                        'is_death' => $request->is_death,
                        'tc_death_id' => $request->tc_death_id,
                        'is_normal' => 0,
                        'load' => null,
                    ];
                    TcFieldObDetail::create($data);
                }else{
                    TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                        ->where('tc_field_tree_id',$request->tc_field_tree_id)
                        ->update([
                            'is_death' => $request->is_death,
                            'tc_death_id' => $request->tc_death_id,
                            'is_normal' => 0,
                            'load' => null,
                        ]);
                }
            }else{
                TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                    ->where('tc_field_tree_id',$request->tc_field_tree_id)->forceDelete();
            }
        }

        if($request->action == 'normal'){
            TcFieldTree::where('id',$request->tc_field_tree_id)->update(['status'=>1]);
            if($request->is_normal != 0){
                $q = TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                    ->where('tc_field_tree_id',$request->tc_field_tree_id)->get();
                if(count($q)==0){
                    $data = [
                        'tc_init_id' => $request->tc_init_id,
                        'tc_field_tree_id' => $request->tc_field_tree_id,
                        'tc_field_ob_id' => $request->tc_field_ob_id,
                        'is_death' => 0,
                        'tc_death_id' => 0,
                        'is_normal' => $request->is_normal,
                    ];
                    TcFieldObDetail::create($data);
                }else{
                    TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                        ->where('tc_field_tree_id',$request->tc_field_tree_id)
                        ->update([
                            'is_death' => 0,
                            'tc_death_id' => 0,
                            'is_normal' => $request->is_normal,
                            'load' => 0,
                        ]);
                }
            }else{
                TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                    ->where('tc_field_tree_id',$request->tc_field_tree_id)->forceDelete();
            }
        }

        if($request->action == 'load'){
            TcFieldObDetail::where('tc_field_ob_id',$request->tc_field_ob_id)
                ->where('tc_field_tree_id',$request->tc_field_tree_id)
                ->update(['load' => $request->load]);
        }

        TcFieldOb::genObsResult($request->tc_field_ob_id);
        return alert(1,null,null);
    }
    public function destroy($id)
    {
        TcFieldOb::where('id',$id)->forceDelete();
        return alert(1,null,'alert-area2');
    }
}
