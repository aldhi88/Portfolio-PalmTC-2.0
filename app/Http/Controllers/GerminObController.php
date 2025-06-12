<?php

namespace App\Http\Controllers;

use App\Models\TcGerminBottle;
use App\Models\TcGerminOb;
use App\Models\TcGerminObDetail;
use App\Models\TcGerminTransaction;
use App\Models\TcGerminTransferBottle;
use App\Models\TcInit;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class GerminObController extends Controller
{
    public function index()
    {
        $data['title'] = "Germination (Per Sample)";
        $data['desc'] = "Display all observation summary by sample data";
        $data['totalSample'] = TcGerminBottle::select('tc_init_id')->groupBy('tc_init_id')->get()->count();
        $data['totalBottle'] = TcGerminBottle::select('*')->sum('bottle_count');
        return view('modules.germin_ob.index',compact('data'));
    }
    public function dt(Request $request)
    {
        $initId = TcGerminBottle::select('tc_init_id')
            ->groupBy('tc_init_id')
            ->get();
        if(count($initId)==0){
            $aryInitId = [];
        }else{
            foreach ($initId as $key => $value) {
                $aryInitId[] = $value->tc_init_id;
            }
        }

        $data = TcInit::select('tc_inits.*')
            ->whereIn('tc_inits.id',$aryInitId)
            ->with([
                'tc_samples'
            ])
            ->withCount([
                'tc_germin_bottles as sum_bottle' => function($q){
                    $q->select(DB::raw('sum(bottle_count)'));
                    // $q->select(DB::raw('sum(bottle_count)'))->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_germin_obs as obs_count' => function($q){
                    $q->where('status',1);
                }
            ])
            ->withCount([
                    'tc_germin_obs as sum_bottle_germin' => function($q){
                        $q->select(DB::raw('sum(total_bottle_germin)'));
                    }
                ])
            ->withCount([
                    'tc_germin_obs as sum_bottle_contam' => function($q){
                        $q->select(DB::raw('sum(total_bottle_contam)'));
                    }
                ])
            ->withCount([
                    'tc_germin_obs as sum_bottle_oxidate' => function($q){
                        $q->select(DB::raw('sum(total_bottle_oxidate)'));
                    }
                ])
            ->withCount([
                    'tc_germin_obs as sum_bottle_other' => function($q){
                        $q->select(DB::raw('sum(total_bottle_other)'));
                    }
                ])
        ;

        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $nextOb = TcGerminOb::where('tc_init_id',$data->id)
                    ->where('status',0)
                    ->get();

                if(count($nextOb)==0){
                    $q = TcGerminOb::create(['tc_init_id' => $data->id]);
                    $nextOb = $q->id;
                }else{
                    $nextOb = $nextOb->first()->id;
                }

                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('germin-obs.show',$data->id)."'>View</a>
                ";
                // $el .= "
                //     <p class='mb-0'>
                //         <a class='text-primary' href='".route('germin-obs.show',$data->id)."'>View</a> -
                //         <a class='text-primary' href='".route('germin-obs.create',$nextOb)."'>Observation</a>
                // ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('sum_bottle_germin_format',function($data){
                return is_null($data->sum_bottle_germin)?0:$data->sum_bottle_germin;
            })
            ->addColumn('sum_bottle_oxidate_format',function($data){
                return is_null($data->sum_bottle_oxidate)?0:$data->sum_bottle_oxidate;
            })
            ->addColumn('sum_bottle_contam_format',function($data){
                return is_null($data->sum_bottle_contam)?0:$data->sum_bottle_contam;
            })
            ->addColumn('sum_bottle_other_format',function($data){
                return is_null($data->sum_bottle_other)?0:$data->sum_bottle_other;
            })

            ->rawColumns(['sample_number_format'])
            ->toJson();
    }

    public function create($initId)
    {
        $data['title'] = "Germination Observation";
        $data['desc'] = "Display germination observation form";
        $data['bottles'] = TcGerminBottle::where('bottle_count','>',0)
            ->get();
        $data['workers'] = TcWorker::where('status',1)->get();
        $qObs = TcGerminOb::where('id',$initId)->with('tc_inits')->with('tc_inits.tc_samples')->first();
        $data['initId'] = $qObs->tc_init_id;
        $allowEditObs = TcGerminTransferBottle::where('tc_germin_ob_id',$initId)
            ->whereRaw('bottle_germin > bottle_left')->get()->count() == 0;
        if($allowEditObs==false){
            return redirect()->route('germin-obs.show', $data['initId']);
        }
        $data['sample'] = $qObs->tc_inits->tc_samples->sample_number_display;
        $data['worker_now'] = $qObs->tc_worker_id;
        $data['date_ob'] = is_null($qObs->ob_date)?false:Carbon::parse($qObs->ob_date)->format('Y-m-d');
        $data['obsId'] = $initId;
        $data['start'] = $qObs->status==0?false:true;
        return view('modules.germin_ob.create',compact('data'));
    }
    public function dtCreate(Request $request)
    {
        $obsId = $request->obsId;
        $initId = $request->initId;
        $obsDate = $request->obsDate;
        $data = TcGerminBottle::select([
                'tc_germin_bottles.*'
            ])
            ->where('tc_init_id',$initId)
            ->where('bottle_date','<=',$obsDate)
            ->with([
                'tc_inits:id,tc_sample_id,created_at',
                'tc_inits.tc_samples'=>function($q){
                    $q->select('id','program','sample_number');
                },
                'tc_workers:id,code',
                'tc_bottles:id,code',
            ])
            ->get()->toArray();

        $qOb = TcGerminObDetail::where('tc_germin_ob_id',$obsId)
            ->get()->toArray();
        $dtOb = collect($qOb);

        $reData = [];
        foreach ($data as $key => $value) {
            $bottleId = $value['id'];
            $bottleStock = $value['status'];
            if($bottleStock==0){
                $cek = $dtOb->where('tc_germin_bottle_id',$bottleId)->count();
                if($cek != 0){
                    $reData [] = $value;
                }
            }else{
                $reData[] = $value;
            }
        }

        $data = collect($reData);

        return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data['bottle_date'])->format('d//m/Y');
            })
            ->addColumn('first_total',function($data) use($initId,$obsId){
                return TcGerminObDetail::firstTotal($initId,$obsId,$data['id']);
            })
            ->addColumn('form_germin',function($data) use($obsId,$initId){
                $q = TcGerminObDetail::where('tc_init_id',$initId)
                    ->where('tc_germin_ob_id',$obsId)
                    ->where('tc_germin_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_germin;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-alpha="'.$data['alpha'].'" data-type="1" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="germin" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_oxidate',function($data) use($obsId,$initId){
                $q = TcGerminObDetail::where('tc_init_id',$initId)
                    ->where('tc_germin_ob_id',$obsId)
                    ->where('tc_germin_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_oxidate;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-alpha="'.$data['alpha'].'" data-type="2" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="oxidate" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_contam',function($data) use($obsId,$initId){
                $q = TcGerminObDetail::where('tc_init_id',$initId)
                    ->where('tc_germin_ob_id',$obsId)
                    ->where('tc_germin_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_contam;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-alpha="'.$data['alpha'].'" data-type="3" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="contam" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_other',function($data) use($obsId,$initId){
                $q = TcGerminObDetail::where('tc_init_id',$initId)
                    ->where('tc_germin_ob_id',$obsId)
                    ->where('tc_germin_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_other;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-alpha="'.$data['alpha'].'" data-type="4" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="other" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('last_total',function($data) use($initId,$obsId){
                return TcGerminObDetail::lastTotal($initId,$obsId,$data['id']);
            })
            ->rawColumns(['form_germin','form_oxidate','form_contam','form_other'])
            ->smart(false)
            ->toJson();
    }
    public function store(Request $request)
    {
        $data['tc_worker_id'] = $request->tc_worker_id;
        $data['ob_date'] = $request->date_ob;
        $data['status'] = 1;
        TcGerminOb::where('id',$request->id)
            ->update($data);
        TcGerminOb::create([
            'status' => 0,
            'tc_init_id' => $request->tc_init_id
        ]);
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
    public function storeObDetail(Request $request)
    {
        $dtDetailPerOb = collect(
            TcGerminObDetail::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                ->with('tc_germin_bottles')->get()->toArray()
        );
        $dtDetailPerBottle = collect(
            TcGerminObDetail::where('tc_germin_ob_id',$request->tc_germin_ob_id)->with('tc_germin_bottles')
                ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)->get()->toArray()
        );

        $stokAwal = TcGerminBottle::firstStock($request->tc_germin_bottle_id);
        $stokAkhir = TcGerminBottle::lastStock($request->tc_germin_bottle_id);
        $dt1 = $request->except('alpha','type','value','tc_worker_id');
        $dt1[$this->aryType($request->type)] = $request->value;

        if(count($dtDetailPerBottle) == 0){ //jika data ob detail untuk bottle itu belum ada
            if(count($dtDetailPerOb) != 0){
                $dataSub = $dtDetailPerOb[0]['tc_germin_bottles']['alpha'];
                // if($request->alpha != $dataSub){
                //     return $this->returnTemplate(0,'Error, alpha is different from before data.');
                // }
            }
            if($request->value != 0){ //hanya proses jika yg diinput tidak 0
                if($stokAkhir >= $request->value){
                    TcGerminObDetail::create($dt1);
                    if($request->type == 1){
                        $dt1['bottle_left'] = $request->value;
                        TcGerminTransferBottle::create($dt1);
                    }

                    $dt2 = $request->except('alpha','type','value');
                    $dt2['first_total'] = $stokAkhir;
                    $dt2['last_total'] = $request->type==1?$stokAkhir:($stokAkhir-$request->value);
                    TcGerminTransaction::storeList($dt2,'in');

                    $this->upTotalInOb($request->tc_germin_ob_id);
                    $this->upStatusBottle($request->tc_germin_bottle_id);
                    return $this->returnTemplate(1,'Success, data has been processed.');
                }
                return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
            }
        }else{ // jika data detail sudah ada
            $cek['bottle_germin'] = $dtDetailPerBottle[0]['bottle_germin'];
            $cek['bottle_oxidate'] = $dtDetailPerBottle[0]['bottle_oxidate'];
            $cek['bottle_contam'] = $dtDetailPerBottle[0]['bottle_contam'];
            $cek['bottle_other'] = $dtDetailPerBottle[0]['bottle_other'];
            $cek[$this->aryType($request->type)] = $request->value;
            $usedStok = $cek['bottle_germin']+$cek['bottle_oxidate']+$cek['bottle_contam']+$cek['bottle_other'];
            $detailId = $dtDetailPerBottle[0]['id'];
            if($stokAwal >= $usedStok){
                if($usedStok == 0){
                    TcGerminObDetail::where('id',$detailId)->forceDelete();
                    TcGerminTransaction::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                        ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)
                        ->forceDelete();
                    TcGerminTransferBottle::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                        ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)
                        ->forceDelete();
                    $this->upTotalInOb($request->tc_germin_ob_id);
                    $this->upStatusBottle($request->tc_germin_bottle_id);
                    return $this->returnTemplate(1,'Success, data has been processed.');
                }else{
                    //update table detailnya
                    TcGerminObDetail::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                        ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)->update($dt1);
                    if($request->type == 1){
                        $q=TcGerminTransferBottle::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                            ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)->get()->count();
                        $dt2['bottle_germin'] = $request->value;
                        $dt2['bottle_left'] = $request->value;
                        if($q==0){
                            $dt1['bottle_left'] = $request->value;
                            TcGerminTransferBottle::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                                ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)
                                ->create($dt1);
                        }else{
                            TcGerminTransferBottle::where('tc_germin_ob_id',$request->tc_germin_ob_id)
                                ->where('tc_germin_bottle_id',$request->tc_germin_bottle_id)
                                ->update($dt2);
                        }
                    }
                    $usedStok = TcGerminObDetail::where('id',$detailId)
                        ->select(DB::raw('(bottle_oxidate+bottle_contam+bottle_other) as usedStok'))
                        ->first()->getAttribute('usedStok');
                    $dt3 = $dt1;
                    $dt3['tc_worker_id'] = $request->tc_worker_id;
                    $dt3['last_total'] = $stokAwal - $usedStok;
                    TcGerminTransaction::storeList($dt3,'up');
                    $this->upTotalInOb($request->tc_germin_ob_id);
                    $this->upStatusBottle($request->tc_germin_bottle_id);
                    return $this->returnTemplate(1,'Success, data has been processed.');
                }
            }
            return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
        }
    }
    public function returnTemplate($type,$msg)
    {
        if($type == 0){
            return [
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area2',
                    'msg' => $msg
                ],
            ];
        }else{
            return [
                'status' => 'success',
                'data' => [
                    'type' => 'success',
                    'icon' => 'check',
                    'el' => 'alert-area2',
                    'msg' => $msg
                ],
            ];
        }
    }
    public function aryType($type)
    {
        $aryType = [
            '1' => 'bottle_germin',
            '2' => 'bottle_oxidate',
            '3' => 'bottle_contam',
            '4' => 'bottle_other',
        ];
        return $aryType[$type];
    }
    public function upTotalInOb($obsId){
        $q = TcGerminObDetail::where('tc_germin_ob_id',$obsId)->get();
        $data['alpha'] = $q->count()==0?null:$q[0]->tc_germin_bottles->alpha;
        $dt = collect($q->toArray());
        $data['total_bottle_germin'] = $dt->sum('bottle_germin');
        $data['total_bottle_oxidate'] = $dt->sum('bottle_oxidate');
        $data['total_bottle_contam'] = $dt->sum('bottle_contam');
        $data['total_bottle_other'] = $dt->sum('bottle_other');
        TcGerminOb::where('id',$obsId)->update($data);
    }
    public function upStatusBottle($bottleId)
    {
        $firstTotal = TcGerminBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcGerminObDetail::where('tc_germin_bottle_id',$bottleId)
            ->get()
            ->toArray();
        $dt = collect($q);
        $lastTotal = $firstTotal - ($dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other'));
        $status = $lastTotal <= 0?0:1;
        TcGerminBottle::where('id',$bottleId)
                ->update(['status' => $status]);
    }

    public function show($id)
    {
        $data['title'] = "Germination (Observation)";
        $data['desc'] = "Display all germination observation data";
        $data['totalBottle'] = TcGerminBottle::where('tc_init_id',$id)->sum('bottle_count');
        $q = collect(TcGerminOb::where('tc_init_id',$id)->get()->toArray());
        $data['obsCount'] = $q->where('status',1)->count();
        $data['totalGermin'] = $q->where('status',1)->sum('total_bottle_germin');
        $data['totalOxidate'] = $q->where('status',1)->sum('total_bottle_oxidate');
        $data['totalContam'] = $q->where('status',1)->sum('total_bottle_contam');
        $data['totalOther'] = $q->where('status',1)->sum('total_bottle_other');

        $q = TcGerminOb::select('id')
            ->where('status',0)
            ->get();

        $data['obId'] = count($q)==0? 0 : $q->first()->id;
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $sumTransfer = TcGerminTransferBottle::where('tc_init_id',$data['initId'])->sum('bottle_left');
        $data['allowObs'] = $sumTransfer == 0;
        return view('modules.germin_ob.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcGerminOb::select([
                'tc_germin_obs.*',
                DB::raw('total_bottle_germin+total_bottle_oxidate+total_bottle_contam+total_bottle_other as grand_total')
            ])
            ->where('status',1)
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_workers'
            ])
        ;
        return DataTables::of($data)
            ->addColumn('ob_date_format',function($data){
                $el = '<p class="mb-0"><strong>'.Carbon::parse($data->ob_date)->format('d/m/Y').'</strong></p>';
                $q = TcGerminTransferBottle::where('tc_germin_ob_id',$data->id)
                    ->whereRaw('bottle_germin > bottle_left')->get()->count();
                if($q == 0){
                    $el .= "<a class='text-primary fs-13' href='".route('germin-obs.create',$data->id)."'>Edit</a>";
                    if($data->grand_total == 0){
                        $el .= " - <a class='text-danger fs-13' href='#delModal' data-toggle='modal' data-target='#delModal' data-attr='".Carbon::parse($data->ob_date)->format('d/m/Y')."' data-id='".$data->id."'>Delete</a>";
                    }
                }
                return $el;
            })
            ->rawColumns(['ob_date_format'])
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $qCode = 'DATE_FORMAT(tc_germin_obs.ob_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_germin_obs.ob_date, 103)';
        }
        $data = TcGerminObDetail::select([
                'tc_germin_ob_details.*',
                DB::raw($qCode.' as ob_date_format')
            ])
            ->leftJoin('tc_germin_obs','tc_germin_obs.id','=','tc_germin_ob_details.tc_germin_ob_id')
            ->where('tc_germin_ob_details.tc_init_id',$request->initId)
            ->with([
                'tc_inits.tc_samples',
                'tc_germin_bottles',
                'tc_germin_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                },
                'tc_germin_obs',
                'tc_germin_obs.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data->tc_germin_bottles->bottle_date)->format('d/m/Y');
            })
            ->filterColumn('ob_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('first_total',function($data){
                $dt['obsId'] = $data->tc_germin_ob_id;
                $dt['bottleId'] = $data->tc_germin_bottle_id;
                return TcGerminTransaction::select('first_total')->where('tc_germin_ob_id',$dt['obsId'])
                    ->where('tc_germin_bottle_id',$dt['bottleId'])->first()->getAttribute('first_total');
            })
            ->addColumn('last_total',function($data){
                $obsId = $data->tc_germin_ob_id;
                $bottleId = $data->tc_germin_bottle_id;
                return TcGerminTransaction::select('last_total')->where('tc_germin_ob_id',$obsId)
                    ->where('tc_germin_bottle_id',$bottleId)->first()->getAttribute('last_total');
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }
    public function destroy($id)
    {
        TcGerminOb::where('id',$id)->forceDelete();
        return alert(1,null,null);
    }

    public function printObsForm(Request $request){
        $data['title'] = "Print Observation Form";
        $data['desc'] = "Printing observation form before input observation result";
        $data['bottles'] = TcGerminBottle::where('status','!=',0)
            ->get();

        return view('modules.germin_ob.print.form_obs',compact('data'));
    }
}
