<?php

namespace App\Http\Controllers;

use App\Models\TcInit;
use App\Models\TcRootingBottle;
use App\Models\TcRootingOb;
use App\Models\TcRootingObDetail;
use App\Models\TcRootingTransaction;
use App\Models\TcRootingTransferBottle;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class RootingObController extends Controller
{
    public function index()
    {
        $data['title'] = "Rooting (Per Sample)";
        $data['desc'] = "Display all observation summary by sample data";
        $data['totalSample'] = TcRootingBottle::select('tc_init_id')->groupBy('tc_init_id')->get()->count();
        $data['totalBottle'] = TcRootingBottle::select('*')->sum('bottle_count');
        return view('modules.rooting_ob.index',compact('data'));
    }
    public function dt(Request $request)
    {
        $initId = TcRootingBottle::select('tc_init_id')
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
                'tc_rooting_bottles as sum_bottle' => function($q){
                    $q->select(DB::raw('sum(bottle_count)'));
                    // $q->select(DB::raw('sum(bottle_count)'))->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_leaf' => function($q){
                    $q->select(DB::raw('SUM(leaf_count)'));
                    // $q->select(DB::raw('SUM(leaf_count)'))->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_rooting_obs as obs_count' => function($q){
                    $q->where('status',1);
                }
            ])
            ->withCount([
                    'tc_rooting_obs as sum_bottle_rooting' => function($q){
                        $q->select(DB::raw('sum(total_leaf_rooting)'));
                    }
                ])
            ->withCount([
                    'tc_rooting_obs as sum_bottle_contam' => function($q){
                        $q->select(DB::raw('sum(total_leaf_contam)'));
                    }
                ])
            ->withCount([
                    'tc_rooting_obs as sum_bottle_oxidate' => function($q){
                        $q->select(DB::raw('sum(total_leaf_oxidate)'));
                    }
                ])
            ->withCount([
                    'tc_rooting_obs as sum_bottle_other' => function($q){
                        $q->select(DB::raw('sum(total_leaf_other)'));
                    }
                ])
        ;

        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $nextOb = TcRootingOb::where('tc_init_id',$data->id)
                    ->where('status',0)
                    ->get();

                if(count($nextOb)==0){
                    $q = TcRootingOb::create(['tc_init_id' => $data->id]);
                    $nextOb = $q->id;
                }else{
                    $nextOb = $nextOb->first()->id;
                }

                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('rooting-obs.show',$data->id)."'>View</a>
                ";
                // $el .= "
                //     <p class='mb-0'>
                //         <a class='text-primary' href='".route('rooting-obs.show',$data->id)."'>View</a> -
                //         <a class='text-primary' href='".route('rooting-obs.create',$nextOb)."'>Observation</a>
                // ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('total_leaf_active',function($data){
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottleLeaf($value->id);
                }
                return $data->first_total_leaf - $usedBottle;
            })
            ->addColumn('sum_bottle_rooting_format',function($data){
                return is_null($data->sum_bottle_rooting)?0:$data->sum_bottle_rooting;
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
        $data['title'] = "Rooting Observation";
        $data['desc'] = "Display rooting observation form";
        $data['bottles'] = TcRootingBottle::where('bottle_count','>',0)->get();
        $data['workers'] = TcWorker::where('status',1)->get();
        $qObs = TcRootingOb::where('id',$initId)->with('tc_inits')->with('tc_inits.tc_samples')->first();
        $data['initId'] = $qObs->tc_init_id;
        $allowEditObs = TcRootingTransferBottle::where('tc_rooting_ob_id',$initId)
            ->whereRaw('bottle_rooting > bottle_left')->get()->count() == 0;
        if($allowEditObs==false){
            return redirect()->route('rooting-obs.show', $data['initId']);
        }
        $data['sample'] = $qObs->tc_inits->tc_samples->sample_number_display;
        $data['worker_now'] = $qObs->tc_worker_id;
        $data['date_ob'] = is_null($qObs->ob_date)?false:Carbon::parse($qObs->ob_date)->format('Y-m-d');
        $data['obsId'] = $initId;
        $data['start'] = $qObs->status==0?false:true;
        return view('modules.rooting_ob.create',compact('data'));
    }
    public function dtCreate(Request $request)
    {
        $obsId = $request->obsId;
        $initId = $request->initId;
        $obsDate = $request->obsDate;
        $data = TcRootingBottle::select([
                'tc_rooting_bottles.*'
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

        $qOb = TcRootingObDetail::where('tc_rooting_ob_id',$obsId)
            ->get()->toArray();
        $dtOb = collect($qOb);

        $reData = [];
        foreach ($data as $key => $value) {
            $bottleId = $value['id'];
            $bottleStock = $value['status'];
            if($bottleStock==0){
                $cek = $dtOb->where('tc_rooting_bottle_id',$bottleId)->count();
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
                // return $data["leaf_count"];
                return TcRootingObDetail::firstTotalLeaf($initId,$obsId,$data['id']);
                // return TcRootingObDetail::firstTotal($initId,$obsId,$data['id']).'<br>'.$data["leaf_count"];
            })
            ->addColumn('form_rooting',function($data) use($obsId,$initId){
                $max = TcRootingObDetail::firstTotal($initId,$obsId,$data['id']);
                $q = TcRootingObDetail::where('tc_init_id',$initId)
                    ->where('tc_rooting_ob_id',$obsId)
                    ->where('tc_rooting_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->leaf_rooting;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = '
                    <input data-root="'.$data['type'].'" max="'.$max.'" data-alpha="'.$data['alpha'].'" data-type="1" data-explant="0" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="rooting_'.$data['id'].'" placeholder="'.$value.'" '.$disabled.'>
                ';
                return $el;
            })
            ->addColumn('form_oxidate',function($data) use($obsId,$initId){
                $q = TcRootingObDetail::where('tc_init_id',$initId)
                    ->where('tc_rooting_ob_id',$obsId)
                    ->where('tc_rooting_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->leaf_oxidate;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-root="'.$data['type'].'" data-alpha="'.$data['alpha'].'" data-type="2" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="oxidate" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_contam',function($data) use($obsId,$initId){
                $q = TcRootingObDetail::where('tc_init_id',$initId)
                    ->where('tc_rooting_ob_id',$obsId)
                    ->where('tc_rooting_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->leaf_contam;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-root="'.$data['type'].'" data-alpha="'.$data['alpha'].'" data-type="3" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="contam" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_other',function($data) use($obsId,$initId){
                $q = TcRootingObDetail::where('tc_init_id',$initId)
                    ->where('tc_rooting_ob_id',$obsId)
                    ->where('tc_rooting_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->leaf_other;
                $disabled = null;
                if($data['bottle_count'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-root="'.$data['type'].'" data-alpha="'.$data['alpha'].'" data-type="4" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-100" name="other" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('last_total',function($data) use($initId,$obsId){
                return TcRootingObDetail::lastTotalLeaf($initId,$obsId,$data['id']);
            })
            ->rawColumns(['form_rooting','form_oxidate','form_contam','form_other','first_total'])
            ->smart(false)
            ->toJson();
    }
    public function store(Request $request)
    {
        $data['tc_worker_id'] = $request->tc_worker_id;
        $data['ob_date'] = $request->date_ob;
        $data['status'] = 1;
        TcRootingOb::where('id',$request->id)->update($data);
        TcRootingTransaction::where('tc_rooting_ob_id',$request->id)->update(['tc_worker_id' => $request->tc_worker_id]);
        $q = TcRootingOb::where('tc_init_id',$request->tc_init_id)->where('status',0)->get()->count();
        if($q == 0){
            TcRootingOb::create([
                'status' => 0,
                'tc_init_id' => $request->tc_init_id
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
    public function storeObDetail(Request $request)
    {
        $dtDetailPerOb = collect(
            TcRootingObDetail::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                ->with('tc_rooting_bottles')->get()->toArray()
        );
        $dtDetailPerBottle = collect(
            TcRootingObDetail::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)->with('tc_rooting_bottles')
                ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->get()->toArray()
        );

        if($request->root == 1){

            $stokAwal = TcRootingBottle::firstStock($request->tc_rooting_bottle_id);
            $stokAkhir = TcRootingBottle::lastStock($request->tc_rooting_bottle_id);
            $stokAwalLeaf = TcRootingBottle::firstStockLeaf2($request->tc_rooting_bottle_id);
            $stokAkhirLeaf = TcRootingBottle::lastStockLeaf2($request->tc_rooting_bottle_id);
            $dt1 = $request->except('alpha','type','value','tc_worker_id','root');
            $dt1[$this->aryType($request->type)] = (int)round($request->value/2);
            $dt1[$this->aryType2($request->type)] = $request->value;

            if(count($dtDetailPerBottle) == 0){ //jika data ob detail untuk bottle itu belum ada
                if(count($dtDetailPerOb) != 0){
                    $dataSub = $dtDetailPerOb[0]['tc_rooting_bottles']['alpha'];
                    if($request->alpha != $dataSub){
                        return $this->returnTemplate(0,'Error, alpha is different from before data.');
                    }
                }
                if($request->value != 0){ //hanya proses jika yg diinput tidak 0
                    if($stokAkhirLeaf >= $request->value){
                        TcRootingObDetail::create($dt1);
                        if($request->type == 1){
                            $dt1['bottle_left'] = (int)round($request->value/2);
                            $dt1['leaf_left'] = $request->value;
                            TcRootingTransferBottle::create($dt1);
                        }

                        $dt2 = $request->except('alpha','type','value','root');
                        $dt2['first_total'] = $stokAkhir;
                        $dt2['first_leaf'] = $stokAkhirLeaf;
                        $dt2['last_total'] = $request->type==1?$stokAkhir:($stokAkhir-((int)round($request->value/2)));
                        $dt2['last_leaf'] = $request->type==1?$stokAkhirLeaf:($stokAkhirLeaf-$request->value);
                        TcRootingTransaction::storeList($dt2,'in');

                        $this->upTotalInOb($request->tc_rooting_ob_id);
                        $this->upStatusBottle($request->tc_rooting_bottle_id);
                        return $this->returnTemplate(1,'Success, data has been processed.');
                    }
                    return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
                }
            }else{ // jika data detail sudah ada
                $cek['leaf_rooting'] = $dtDetailPerBottle[0]['leaf_rooting'];
                $cek['leaf_oxidate'] = $dtDetailPerBottle[0]['leaf_oxidate'];
                $cek['leaf_contam'] = $dtDetailPerBottle[0]['leaf_contam'];
                $cek['leaf_other'] = $dtDetailPerBottle[0]['leaf_other'];
                $cek[$this->aryType2($request->type)] = $request->value;
                $usedStok = $cek['leaf_rooting']+$cek['leaf_oxidate']+$cek['leaf_contam']+$cek['leaf_other'];
                $detailId = $dtDetailPerBottle[0]['id'];
                if($stokAwalLeaf >= $usedStok){
                    if($usedStok == 0){
                        TcRootingObDetail::where('id',$detailId)->forceDelete();
                        TcRootingTransaction::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                            ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)
                            ->forceDelete();
                        TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                            ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)
                            ->forceDelete();
                        $this->upTotalInOb($request->tc_rooting_ob_id);
                        $this->upStatusBottle($request->tc_rooting_bottle_id);
                        return $this->returnTemplate(1,'Success, data has been processed.');
                    }else{
                        //update table detailnya
                        TcRootingObDetail::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                            ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->update($dt1);
                        if($request->type == 1){
                            $q=TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                                ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->get()->count();
                            $dt2['bottle_rooting'] = (int)round($request->value/2);
                            $dt2['leaf_rooting'] = $request->value;
                            $dt2['bottle_left'] = (int)round($request->value/2);
                            $dt2['leaf_left'] = $request->value;
                            if($q==0){
                                $dt1['bottle_left'] = $request->value;
                                TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                                    ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->create($dt1);
                            }else{
                                TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                                    ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->update($dt2);
                            }
                        }
                        $usedStok = TcRootingObDetail::where('id',$detailId)
                            ->select(DB::raw('(bottle_oxidate+bottle_contam+bottle_other) as usedStok'))
                            ->first()->getAttribute('usedStok');
                        $usedStokLeaf = TcRootingObDetail::where('id',$detailId)
                            ->select(DB::raw('(leaf_oxidate+leaf_contam+leaf_other) as usedStokLeaf'))
                            ->first()->getAttribute('usedStokLeaf');
                        $dt3 = $dt1;
                        $dt3['tc_worker_id'] = $request->tc_worker_id;
                        $dt3['last_total'] = $stokAwal - $usedStok;
                        $dt3['last_leaf'] = $stokAwalLeaf - $usedStokLeaf;
                        TcRootingTransaction::storeList($dt3,'up');
                        $this->upTotalInOb($request->tc_rooting_ob_id);
                        $this->upStatusBottle($request->tc_rooting_bottle_id);
                        return $this->returnTemplate(1,'Success, data has been processed.');
                    }
                }
                return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
            }

        }else{

            $stokAwal = TcRootingBottle::firstStock($request->tc_rooting_bottle_id);
            $stokAwalLeaf = TcRootingBottle::firstStockLeaf($request->tc_rooting_bottle_id);
            $dt1 = $request->except('alpha','type','value','tc_worker_id','root');
            $dt1[$this->aryType($request->type)] = $request->value;
            $dt1[$this->aryType2($request->type)] = $request->value;

            if(count($dtDetailPerBottle) == 0){ //jika data ob detail untuk bottle itu belum ada
                if(count($dtDetailPerOb) != 0){
                    $dataSub = $dtDetailPerOb[0]['tc_rooting_bottles']['alpha'];
                    if($request->alpha != $dataSub){
                        return $this->returnTemplate(0,'Error, alpha is different from before data.');
                    }
                }
                if($request->value != 0){ //hanya proses jika yg diinput tidak 0
                    if($stokAwalLeaf >= $request->value){
                        TcRootingObDetail::create($dt1);
                        if($request->type == 1){
                            $dt1['bottle_left'] = $request->value;
                            $dt1['leaf_left'] = $request->value;
                            TcRootingTransferBottle::create($dt1);
                        }

                        $dt2 = $request->except('alpha','type','value','root');
                        $dt2['first_total'] = $stokAwal;
                        $dt2['first_leaf'] = $stokAwalLeaf;
                        $dt2['last_total'] = $request->type==1?$stokAwal:($stokAwal-($request->value));
                        $dt2['last_leaf'] = $request->type==1?$stokAwalLeaf:($stokAwalLeaf-$request->value);
                        TcRootingTransaction::storeList($dt2,'in');

                        $this->upTotalInOb($request->tc_rooting_ob_id);
                        $this->upStatusBottle($request->tc_rooting_bottle_id);
                        return $this->returnTemplate(1,'Success, data has been processed.');
                    }
                    return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
                }
            }else{ // jika data detail sudah ada
                $cek['leaf_rooting'] = $dtDetailPerBottle[0]['leaf_rooting'];
                $cek['leaf_oxidate'] = $dtDetailPerBottle[0]['leaf_oxidate'];
                $cek['leaf_contam'] = $dtDetailPerBottle[0]['leaf_contam'];
                $cek['leaf_other'] = $dtDetailPerBottle[0]['leaf_other'];
                $cek[$this->aryType2($request->type)] = $request->value;
                $usedStok = $cek['leaf_rooting']+$cek['leaf_oxidate']+$cek['leaf_contam']+$cek['leaf_other'];
                $detailId = $dtDetailPerBottle[0]['id'];
                if($stokAwalLeaf >= $usedStok){
                    if($usedStok == 0){
                        TcRootingObDetail::where('id',$detailId)->forceDelete();
                        TcRootingTransaction::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                            ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)
                            ->forceDelete();
                        TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                            ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)
                            ->forceDelete();
                        $this->upTotalInOb($request->tc_rooting_ob_id);
                        $this->upStatusBottle($request->tc_rooting_bottle_id);
                        return $this->returnTemplate(1,'Success, data has been processed.');
                    }else{
                        //update table detailnya
                        TcRootingObDetail::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                            ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->update($dt1);
                        if($request->type == 1){
                            $q=TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                                ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->get()->count();
                            $dt2['bottle_rooting'] = $request->value;
                            $dt2['leaf_rooting'] = $request->value;
                            $dt2['bottle_left'] = $request->value;
                            $dt2['leaf_left'] = $request->value;
                            if($q==0){
                                $dt1['bottle_left'] = $request->value;
                                TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                                    ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->create($dt1);
                            }else{
                                TcRootingTransferBottle::where('tc_rooting_ob_id',$request->tc_rooting_ob_id)
                                    ->where('tc_rooting_bottle_id',$request->tc_rooting_bottle_id)->update($dt2);
                            }
                        }
                        $usedStok = TcRootingObDetail::where('id',$detailId)
                            ->select(DB::raw('(bottle_oxidate+bottle_contam+bottle_other) as usedStok'))
                            ->first()->getAttribute('usedStok');
                        $usedStokLeaf = TcRootingObDetail::where('id',$detailId)
                            ->select(DB::raw('(leaf_oxidate+leaf_contam+leaf_other) as usedStokLeaf'))
                            ->first()->getAttribute('usedStokLeaf');
                        $dt3 = $dt1;
                        $dt3['tc_worker_id'] = $request->tc_worker_id;
                        $dt3['last_total'] = $stokAwal - $usedStok;
                        $dt3['last_leaf'] = $stokAwalLeaf - $usedStokLeaf;
                        TcRootingTransaction::storeList($dt3,'up');
                        $this->upTotalInOb($request->tc_rooting_ob_id);
                        $this->upStatusBottle($request->tc_rooting_bottle_id);
                        return $this->returnTemplate(1,'Success, data has been processed.');
                    }
                }
                return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
            }

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
            '1' => 'bottle_rooting',
            '2' => 'bottle_oxidate',
            '3' => 'bottle_contam',
            '4' => 'bottle_other',
        ];
        return $aryType[$type];
    }
    public function aryType2($type)
    {
        $aryType = [
            '1' => 'leaf_rooting',
            '2' => 'leaf_oxidate',
            '3' => 'leaf_contam',
            '4' => 'leaf_other',
        ];
        return $aryType[$type];
    }
    public function upTotalInOb($obsId)
    {
        $q = TcRootingObDetail::where('tc_rooting_ob_id',$obsId)->get();
        $data['alpha'] = $q->count()==0?null:$q[0]->tc_rooting_bottles->alpha;
        $dt = collect($q->toArray());
        $data['total_bottle_rooting'] = $dt->sum('bottle_rooting');
        $data['total_bottle_oxidate'] = $dt->sum('bottle_oxidate');
        $data['total_bottle_contam'] = $dt->sum('bottle_contam');
        $data['total_bottle_other'] = $dt->sum('bottle_other');
        $data['total_leaf_rooting'] = $dt->sum('leaf_rooting');
        $data['total_leaf_oxidate'] = $dt->sum('leaf_oxidate');
        $data['total_leaf_contam'] = $dt->sum('leaf_contam');
        $data['total_leaf_other'] = $dt->sum('leaf_other');
        TcRootingOb::where('id',$obsId)->update($data);
    }
    public function upStatusBottle($bottleId)
    {
        $firstTotal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcRootingObDetail::where('tc_rooting_bottle_id',$bottleId)->get()->toArray();
        $dt = collect($q);
        $lastTotal = $firstTotal - ($dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other'));
        $status = $lastTotal <= 0?0:1;
        TcRootingBottle::where('id',$bottleId)
                ->update(['status' => $status]);
    }

    public function show($id)
    {
        $data['title'] = "Rooting (Observation)";
        $data['desc'] = "Display all rooting observation data";
        $data['totalPlantlet'] = TcRootingBottle::where('tc_init_id',$id)->sum('leaf_count');
        $q = TcRootingBottle::select('id')->where('tc_init_id',$id)->get();
        $usedPlantlet = 0;
        foreach ($q as $key => $value) {
            $usedPlantlet += TcRootingBottle::usedBottleLeaf($value->id);
        }
        $data['totalBottle'] = $data['totalPlantlet'] - $usedPlantlet;
        $q = collect(TcRootingOb::where('tc_init_id',$id)->get()->toArray());
        $data['obsCount'] = $q->where('status',1)->count();
        $data['totalRooting'] = $q->where('status',1)->sum('total_leaf_rooting');
        $data['totalOxidate'] = $q->where('status',1)->sum('total_leaf_oxidate');
        $data['totalContam'] = $q->where('status',1)->sum('total_leaf_contam');
        $data['totalOther'] = $q->where('status',1)->sum('total_leaf_other');

        $q = TcRootingOb::select('id')->where('status',0)->get();

        $data['obId'] = count($q)==0? 0 : $q->first()->id;
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $sumTransfer = TcRootingTransferBottle::where('tc_init_id',$data['initId'])->sum('bottle_left');
        $data['allowObs'] = $sumTransfer == 0;
        return view('modules.rooting_ob.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcRootingOb::select([
                'tc_rooting_obs.*',
                DB::raw('total_bottle_rooting+total_bottle_oxidate+total_bottle_contam+total_bottle_other as grand_total')
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
                $q = TcRootingTransferBottle::where('tc_rooting_ob_id',$data->id)
                    ->whereRaw('bottle_rooting > bottle_left')->get()->count();
                if($q == 0){
                    $el .= "<a class='text-primary fs-13' href='".route('rooting-obs.create',$data->id)."'>Edit</a>";
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
        $qCode = 'DATE_FORMAT(tc_rooting_obs.ob_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_rooting_obs.ob_date, 103)';
        }
        $data = TcRootingObDetail::select([
                'tc_rooting_ob_details.*',
                DB::raw($qCode.' as ob_date_format')
            ])
            ->leftJoin('tc_rooting_obs','tc_rooting_obs.id','=','tc_rooting_ob_details.tc_rooting_ob_id')
            ->where('tc_rooting_ob_details.tc_init_id',$request->initId)
            ->with([
                'tc_inits.tc_samples',
                'tc_rooting_bottles',
                'tc_rooting_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                },
                'tc_rooting_obs',
                'tc_rooting_obs.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data->tc_rooting_bottles->bottle_date)->format('d/m/Y');
            })
            ->filterColumn('ob_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('first_total',function($data){
                $dt['obsId'] = $data->tc_rooting_ob_id;
                $dt['bottleId'] = $data->tc_rooting_bottle_id;
                return TcRootingTransaction::select('first_total')->where('tc_rooting_ob_id',$dt['obsId'])
                    ->where('tc_rooting_bottle_id',$dt['bottleId'])->first()->getAttribute('first_total');
                // return TcRootingObDetail::firstTotalLeaf($data->tc_init_id,$data->tc_rooting_ob_id,$data->tc_rooting_bottle_id);
            })
            ->addColumn('last_total',function($data){
                $obsId = $data->tc_rooting_ob_id;
                $bottleId = $data->tc_rooting_bottle_id;
                return TcRootingTransaction::select('last_leaf')->where('tc_rooting_ob_id',$obsId)
                    ->where('tc_rooting_bottle_id',$bottleId)->first()->getAttribute('last_leaf');
                // return TcRootingObDetail::lastTotalLeafObs($data->tc_init_id,$data->tc_rooting_ob_id,$data->tc_rooting_bottle_id);
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }
    public function destroy($id)
    {
        TcRootingOb::where('id',$id)->forceDelete();
        return alert(1,null,'alert-area2');
    }
    public function printObsForm(Request $request){
        $data['title'] = "Print Observation Form";
        $data['desc'] = "Printing observation form before input observation result";
        $data['bottles'] = TcRootingBottle::where('status','!=',0)
            ->get();

        return view('modules.rooting_ob.print.form_obs',compact('data'));
    }
}
