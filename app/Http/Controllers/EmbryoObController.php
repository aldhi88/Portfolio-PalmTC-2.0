<?php

namespace App\Http\Controllers;

use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoList;
use App\Models\TcEmbryoOb;
use App\Models\TcEmbryoObDetail;
use App\Models\TcEmbryoTransferBottle;
use App\Models\TcInit;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class EmbryoObController extends Controller
{
    public function index()
    {
        $data['title'] = "Embryogenesis (Per Sample)";
        $data['desc'] = "Display all observation summary by sample data";
        $countSample = TcEmbryoBottle::select('tc_init_id')->groupBy('tc_init_id')->get()->count();
        $data['totalSample'] = $countSample;
        $data['totalBottle'] = TcEmbryoBottle::select('*')->sum('number_of_bottle');
        return view('modules.embryo_ob.index',compact('data'));
    }
    public function dt(Request $request)
    {
        $initId = TcEmbryoBottle::select('tc_init_id')->groupBy('tc_init_id')->get()->toArray();
        $aryInitId = [];
        if(count($initId)!=0){
            $aryInitId = array_column($initId,'tc_init_id');
        }

        $data = TcInit::select('tc_inits.*')
            ->whereIn('tc_inits.id',$aryInitId)
            ->with([
                'tc_samples'
            ])
            ->withCount([
                'tc_embryo_bottles as sum_bottle' => function($q){
                    $q->select(DB::raw('sum(number_of_bottle)'));
                    // $q->select(DB::raw('sum(number_of_bottle)'))->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_embryo_obs as obs_count' => function($q){
                    $q->where('status',1);
                }
            ])
            ->withCount([
                    'tc_embryo_obs as sum_bottle_embryo' => function($q){
                        $q->select(DB::raw('sum(total_bottle_embryo)'));
                    }
                ])
            ->withCount([
                    'tc_embryo_obs as sum_bottle_contam' => function($q){
                        $q->select(DB::raw('sum(total_bottle_contam)'));
                    }
                ])
            ->withCount([
                    'tc_embryo_obs as sum_bottle_oxidate' => function($q){
                        $q->select(DB::raw('sum(total_bottle_oxidate)'));
                    }
                ])
            ->withCount([
                    'tc_embryo_obs as sum_bottle_other' => function($q){
                        $q->select(DB::raw('sum(total_bottle_other)'));
                    }
                ])
        ;
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $nextOb = TcEmbryoOb::nextOb($data->id);
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".route('embryo-obs.show',$data->id)."'>View</a>
                ";
                // $el .= "
                //     <p class='mb-0'>
                //         <a class='text-primary' href='".route('embryo-obs.show',$data->id)."'>View</a> -
                //         <a class='text-primary' href='".route('embryo-obs.create',$nextOb)."'>Observation</a>
                // ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('sum_bottle_embryo_format',function($data){
                return is_null($data->sum_bottle_embryo)?0:$data->sum_bottle_embryo;
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

    public function create($id)
    {
        $data['title'] = "Embryo Observation";
        $data['desc'] = "Display embryo observation form";
        $data['bottles'] = TcEmbryoBottle::where('number_of_bottle','>',0)
            ->get();
        $data['workers'] = TcWorker::where('status',1)->get();
        $qObs = TcEmbryoOb::where('id',$id)
            ->with('tc_inits')
            ->with('tc_inits.tc_samples')
            ->first();
        $data['initId'] = $qObs->tc_init_id;

        $allowEditObs = TcEmbryoTransferBottle::where('tc_embryo_ob_id',$id)
            ->whereRaw('bottle_embryo > bottle_left')->get()->count() == 0;
        if($allowEditObs==false){
            return redirect()->route('embryo-obs.show', $data['initId']);
        }
        $data['sample'] = $qObs->tc_inits->tc_samples->sample_number_display;
        $data['worker_now'] = $qObs->tc_worker_id;
        $data['date_ob'] = is_null($qObs->work_date)?false:Carbon::parse($qObs->work_date)->format('Y-m-d');
        $data['obsId'] = $id;
        $data['start'] = $qObs->status==0?false:true;
        return view('modules.embryo_ob.create',compact('data'));
    }
    public function dtCreate(Request $request)
    {
        $obsId = $request->obsId;
        $initId = $request->initId;
        $obsDate = $request->obsDate;
        $data = TcEmbryoBottle::select([
            'tc_embryo_bottles.*'
        ])
        ->where('tc_init_id',$initId)
        ->where('bottle_date','<=',$obsDate)
        ->with([
            'tc_inits:id,tc_sample_id,created_at',
            'tc_inits.tc_samples'=>function($q){
                $q->select('id','program','sample_number');
            },
            'tc_workers:id,code',
        ])
        ->get()->toArray();

        $qOb = TcEmbryoObDetail::where('tc_embryo_ob_id',$obsId)
            ->get()->toArray();
        $dtOb = collect($qOb);

        $reData = [];
        foreach ($data as $key => $value) {
            $bottleId = $value['id'];
            $bottleStock = $value['status'];
            if($bottleStock==0){
                $cek = $dtOb->where('tc_embryo_bottle_id',$bottleId)->count();
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
                return TcEmbryoObDetail::firstTotal($initId,$obsId,$data['id']);
            })
            ->addColumn('form_embryo',function($data) use($obsId,$initId){
                $q = TcEmbryoObDetail::where('tc_init_id',$initId)
                    ->where('tc_embryo_ob_id',$obsId)
                    ->where('tc_embryo_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_embryo;
                $disabled = null;
                if($data['number_of_bottle'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-sub="'.$data['sub'].'" data-type="1" data-id="'.$data['id'].'" type="text" class="form-obs text-center border border-primary pl-1 w-50" name="embryo" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_oxidate',function($data) use($obsId,$initId){
                $q = TcEmbryoObDetail::where('tc_init_id',$initId)
                    ->where('tc_embryo_ob_id',$obsId)
                    ->where('tc_embryo_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_oxidate;
                $disabled = null;
                if($data['number_of_bottle'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-sub="'.$data['sub'].'" data-type="2" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-50" name="oxidate" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_contam',function($data) use($obsId,$initId){
                $q = TcEmbryoObDetail::where('tc_init_id',$initId)
                    ->where('tc_embryo_ob_id',$obsId)
                    ->where('tc_embryo_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_contam;
                $disabled = null;
                if($data['number_of_bottle'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-sub="'.$data['sub'].'" data-type="3" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-50" name="contam" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('form_other',function($data) use($obsId,$initId){
                $q = TcEmbryoObDetail::where('tc_init_id',$initId)
                    ->where('tc_embryo_ob_id',$obsId)
                    ->where('tc_embryo_bottle_id',$data['id'])
                    ->get();
                $value = count($q)==0?0:$q->first()->bottle_other;
                $disabled = null;
                if($data['number_of_bottle'] == 0){
                    $disabled = count($q)==0?'disabled':null;
                }
                $el = ' <input data-sub="'.$data['sub'].'" data-type="4" data-id="'.$data['id'].'" type="text" class="form-obs text-center text-danger pl-1 w-50" name="other" placeholder="'.$value.'" '.$disabled.'> ';
                return $el;
            })
            ->addColumn('last_total',function($data) use($initId,$obsId){
                return TcEmbryoObDetail::lastTotal($initId,$obsId,$data['id']);
            })
            ->rawColumns(['form_embryo','form_oxidate','form_contam','form_other'])
            ->smart(false)
            ->toJson();
    }
    public function store(Request $request)
    {
        $data['tc_worker_id'] = $request->tc_worker_id;
        $data['work_date'] = $request->date_ob;
        $data['status'] = 1;
        TcEmbryoOb::where('id',$request->id)->update($data);
        TcEmbryoList::where('tc_embryo_ob_id',$request->id)->update(['tc_worker_id' => $request->tc_worker_id]);
        TcEmbryoObDetail::where('tc_embryo_ob_id',$request->id)->update(['tc_worker_id' => $request->tc_worker_id]);
        TcEmbryoTransferBottle::where('tc_embryo_ob_id',$request->id)->update(['tc_worker_id' => $request->tc_worker_id]);
        $q = TcEmbryoOb::where('tc_init_id',$request->tc_init_id)->where('status',0)->get()->count();
        if($q == 0){
            TcEmbryoOb::create([
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
            TcEmbryoObDetail::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                ->with('tc_embryo_bottles')->get()->toArray()
        );
        $dtDetailPerBottle = collect(
            TcEmbryoObDetail::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)->with('tc_embryo_bottles')
                ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)->get()->toArray()
        );
        // dump($request->all());
        $stokAwal = TcEmbryoBottle::firstStock($request->tc_embryo_bottle_id);
        $stokAkhir = TcEmbryoBottle::lastStock($request->tc_embryo_bottle_id);
        // dd($stokAwal);
        $dt1 = $request->except('sub','type','value');
        $dt1[$this->aryType($request->type)] = $request->value;

        if(count($dtDetailPerBottle) == 0){ //jika data ob detail untuk bottle itu belum ada
            if(count($dtDetailPerOb) != 0){
                $dataSub = $dtDetailPerOb[0]['tc_embryo_bottles']['sub'];
                // if($request->sub != $dataSub){
                //     return $this->returnTemplate(0,'Error, sub culture is different from before data.');
                // }
            }
            if($request->value != 0){ //hanya proses jika yg diinput tidak 0
                if($stokAkhir >= $request->value){
                    TcEmbryoObDetail::create($dt1);
                    if($request->type == 1){
                        $dt1['bottle_left'] = $request->value;
                        TcEmbryoTransferBottle::create($dt1);
                    }
                    $dt2 = $request->except('sub','type','value');
                    $dt2['first_total'] = $stokAkhir;
                    $dt2['last_total'] = $request->type==1?$stokAkhir:($stokAkhir-$request->value);
                    TcEmbryoList::storeList($dt2,'in');

                    $this->upTotalInOb($request->tc_embryo_ob_id, $request->tc_embryo_bottle_id);
                    $this->upStatusBottle($request->tc_embryo_bottle_id);
                    return $this->returnTemplate(1,'Success, data has been processed.');
                }
                return $this->returnTemplate(0,'Error, bottle count is bigger than bottle total.');
            }
        }else{ // jika data detail sudah ada
            $cek['bottle_embryo'] = $dtDetailPerBottle[0]['bottle_embryo'];
            $cek['bottle_oxidate'] = $dtDetailPerBottle[0]['bottle_oxidate'];
            $cek['bottle_contam'] = $dtDetailPerBottle[0]['bottle_contam'];
            $cek['bottle_other'] = $dtDetailPerBottle[0]['bottle_other'];
            $cek[$this->aryType($request->type)] = $request->value;
            $usedStok = $cek['bottle_embryo']+$cek['bottle_oxidate']+$cek['bottle_contam']+$cek['bottle_other'];
            $detailId = $dtDetailPerBottle[0]['id'];
            if($stokAwal >= $usedStok){
                if($usedStok == 0){
                    // $this->upTotalInOb($request->tc_embryo_ob_id); //gak cocok karena gak set jadi 0 di embryo_obs
                    TcEmbryoOb::where('id', $request->tc_embryo_ob_id)
                        ->update([
                            'sub' => null,
                            'total_bottle_embryo' => 0,
                            'total_bottle_oxidate' => 0,
                            'total_bottle_contam' => 0,
                            'total_bottle_other' => 0,
                        ]);

                    $this->upStatusBottle($request->tc_embryo_bottle_id);

                    TcEmbryoObDetail::where('id',$detailId)->forceDelete();
                    TcEmbryoList::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                        ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)
                        ->forceDelete();
                    TcEmbryoTransferBottle::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                        ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)
                        ->forceDelete();

                    return $this->returnTemplate(1,'Success, data has been processed.');
                }else{
                    //update table detailnya
                    TcEmbryoObDetail::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                        ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)->update($dt1);
                    if($request->type == 1){
                        $q=TcEmbryoTransferBottle::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                            ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)->get()->count();
                        $dt2['tc_worker_id'] = $request->tc_worker_id;
                        $dt2['bottle_embryo'] = $request->value;
                        $dt2['bottle_left'] = $request->value;
                        if($q==0){
                            $dt1['bottle_left'] = $request->value;
                            TcEmbryoTransferBottle::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                                ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)
                                ->create($dt1);
                        }else{
                            TcEmbryoTransferBottle::where('tc_embryo_ob_id',$request->tc_embryo_ob_id)
                                ->where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)
                                ->update($dt2);
                        }
                    }
                    $dt3 = $dt1;
                    $stokAwal = TcEmbryoList::where('tc_embryo_bottle_id',$request->tc_embryo_bottle_id)
                        ->select('first_total')
                        ->orderBy('id','desc')
                        ->first()
                        ->getAttribute('first_total');
                    // dump($request->all(), $usedStok, $cek);
                    $dt3['last_total'] = $stokAwal - ($usedStok-$cek['bottle_embryo']);
                    TcEmbryoList::storeList($dt3,'up');
                    $this->upTotalInOb($request->tc_embryo_ob_id, $request->tc_embryo_bottle_id);
                    $this->upStatusBottle($request->tc_embryo_bottle_id);
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
            '1' => 'bottle_embryo',
            '2' => 'bottle_oxidate',
            '3' => 'bottle_contam',
            '4' => 'bottle_other',
        ];
        return $aryType[$type];
    }
    public function upTotalInOb($obsId, $tc_embryo_bottle_id)
    {
        $q = TcEmbryoObDetail::where('tc_embryo_ob_id',$obsId)->get();
        $data['sub'] = $q->count()==0?null:$q[0]->tc_embryo_bottles->sub;
        $dt = collect($q->toArray());
        $data['total_bottle_embryo'] = $dt->sum('bottle_embryo');
        $data['total_bottle_oxidate'] = $dt->sum('bottle_oxidate');
        $data['total_bottle_contam'] = $dt->sum('bottle_contam');
        $data['total_bottle_other'] = $dt->sum('bottle_other');
        TcEmbryoOb::where('id',$obsId)->update($data);
    }
    public function upStatusBottle($bottleId)
    {
        $firstTotal = TcEmbryoBottle::where('id',$bottleId)->first()->getAttribute('number_of_bottle');
        $q = TcEmbryoObDetail::where('tc_embryo_bottle_id',$bottleId)
            ->get()
            ->toArray();
        $dt = collect($q);
        $lastTotal = $firstTotal - ($dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other'));
        $status = $lastTotal <= 0?0:1;
        TcEmbryoBottle::where('id',$bottleId)
                ->update(['status' => $status]);
    }

    public function show($id)
    {
        $data['title'] = "Embryogenesis (Observation)";
        $data['desc'] = "Display all embryogenesis observation data";
        $data['totalBottle'] = TcEmbryoBottle::where('tc_init_id',$id)->sum('number_of_bottle');
        $q = collect(TcEmbryoOb::where('tc_init_id',$id)->get()->toArray());
        $data['obsCount'] = $q->where('status',1)->count();
        $data['totalEmbryo'] = $q->where('status',1)->sum('total_bottle_embryo');
        $data['totalOxidate'] = $q->where('status',1)->sum('total_bottle_oxidate');
        $data['totalContam'] = $q->where('status',1)->sum('total_bottle_contam');
        $data['totalOther'] = $q->where('status',1)->sum('total_bottle_other');
        $q = TcEmbryoOb::select('id')->where('status',0)->get();

        $data['initId'] = $id;
        $data['nextOb'] = TcEmbryoOb::nextOb($id);
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['allowObs'] = TcEmbryoTransferBottle::where('tc_init_id', $id)->sum('bottle_left') == 0?true:false;
        return view('modules.embryo_ob.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcEmbryoOb::select([
                'tc_embryo_obs.*',
                DB::raw('total_bottle_embryo+total_bottle_oxidate+total_bottle_contam+total_bottle_other as grand_total')
            ])
            ->where('status',1)
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_workers'
            ])
        ;
        return DataTables::of($data)
            ->addColumn('work_date_format',function($data){
                $q = TcEmbryoTransferBottle::where('tc_embryo_ob_id',$data->id)
                    ->whereRaw('bottle_embryo > bottle_left')->get()->count();
                $el = '<p class="mb-0"><strong>'.Carbon::parse($data->work_date)->format('d/m/Y').'</strong></p>';
                if($q == 0){
                    if($data->tc_worker_id != 0){
                        $el .= "<a class='text-primary fs-13' href='".route('embryo-obs.create',$data->id)."'>Edit</a>";
                    }
                    if($data->grand_total == 0){
                        $el .= " - <a class='text-danger fs-13' href='#delModal' data-toggle='modal' data-target='#delModal' data-attr='".Carbon::parse($data->work_date)->format('d/m/Y')."' data-id='".$data->id."'>Delete</a>";
                    }
                }
                return $el;
            })
            ->rawColumns(['work_date_format'])
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $data = TcEmbryoObDetail::select([
                'tc_embryo_ob_details.*',
                DB::raw('convert(varchar,tc_embryo_obs.work_date, 103) as work_date_format')
            ])
            ->leftJoin('tc_embryo_obs','tc_embryo_obs.id','=','tc_embryo_ob_details.tc_embryo_ob_id')
            ->where('tc_embryo_ob_details.tc_init_id',$request->initId)
            ->with([
                'tc_inits:id,tc_sample_id',
                'tc_inits.tc_samples' => function($q){
                    $q->select('id','program','sample_number');
                },
                'tc_embryo_bottles:id,bottle_date,sub,tc_worker_id',
                'tc_embryo_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                },
                'tc_workers:id,code',
                'tc_embryo_obs:id,work_date',
            ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('bottle_date_format',function($data){
                return Carbon::parse($data->tc_embryo_bottles->bottle_date)->format('d/m/Y');
            })
            ->filterColumn('work_date_format', function($query, $keyword){
                $sql = 'convert(varchar,tc_embryo_obs.work_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('first_total',function($data){
                $dt['obsId'] = $data->tc_embryo_ob_id;
                $dt['bottleId'] = $data->tc_embryo_bottle_id;
                return TcEmbryoList::select('first_total')->where('tc_embryo_ob_id',$dt['obsId'])
                    ->where('tc_embryo_bottle_id',$dt['bottleId'])->first()->getAttribute('first_total');
            })
            ->addColumn('last_total',function($data){
                $obsId = $data->tc_embryo_ob_id;
                $bottleId = $data->tc_embryo_bottle_id;
                return TcEmbryoList::select('last_total')->where('tc_embryo_ob_id',$obsId)
                    ->where('tc_embryo_bottle_id',$bottleId)->first()->getAttribute('last_total');
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }
    public function destroy($id)
    {
        TcEmbryoOb::where('id',$id)->forceDelete();
        return alert(1,null,null);
    }

    public function printObsForm(Request $request){
        $data['title'] = "Print Observation Form";
        $data['desc'] = "Printing observation form before input observation result";
        $data['bottles'] = TcEmbryoBottle::where('status','!=',0)
            ->get();

        return view('modules.embryo_ob.print.form_obs',compact('data'));
    }
}
