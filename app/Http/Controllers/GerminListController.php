<?php

namespace App\Http\Controllers;

use App\Models\TcBottleInit;
use App\Models\TcBottleInitDetail;
use App\Models\TcInit;
use App\Models\TcGerminBottle;
use App\Models\TcGerminComment;
use App\Models\TcGerminOb;
use App\Models\TcGerminObDetail;
use App\Models\TcGerminTransaction;
use App\Models\TcGerminTransfer;
use App\Models\TcGerminTransferBottle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GerminListController extends Controller
{
    public function index()
    {
        $data['title'] = "Germination Per Sample";
        $data['desc'] = "Display all available data";
        $data['column1'] = TcBottleInit::where('keyword','germin_column1')->first()->getAttribute('column_name');
        $data['column2'] = TcBottleInit::where('keyword','germin_column2')->first()->getAttribute('column_name');
        return view('modules.germin_list.index',compact('data'));
    }
    public function rollbackImport($id)
    {
        TcGerminBottle::where('tc_init_id', $id)->forceDelete();
        TcGerminTransaction::where('tc_init_id', $id)->forceDelete();
        TcGerminOb::where('tc_init_id', $id)->forceDelete();
        TcGerminObDetail::where('tc_init_id', $id)->forceDelete();
        TcGerminTransferBottle::where('tc_init_id', $id)->forceDelete();
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
    public function dt()
    {
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q){
                $q->where('keyword','germin_column1');
            })->get()->toArray();
        $aryBottleCol1 = array_column($q, 'tc_bottle_id');
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q){
                $q->where('keyword','germin_column2');
            })->get()->toArray();
        $aryBottleCol2 = array_column($q, 'tc_bottle_id');
        $data = TcInit::select(['tc_inits.*'])
            ->whereHas('tc_germin_bottles')
            ->with([
                'tc_samples',
                'tc_germin_bottles:id,tc_init_id,tc_worker_id,tc_laminar_id',
                'tc_germin_transactions:id,tc_init_id,tc_worker_id',
            ])
            ->withCount([
                'tc_germin_bottles as first_total' => function($q){
                    $q->select(DB::raw('SUM(bottle_count)'))->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_germin_bottles as first_total_column1' => function($q) use($aryBottleCol1){
                    $q->select(DB::raw('SUM(bottle_count)'))->whereIn('tc_bottle_id',$aryBottleCol1)
                    ->where('status','!=',0);
                }
            ])
            ->withCount([
                'tc_germin_bottles as first_total_column2' => function($q) use($aryBottleCol2){
                    $q->select(DB::raw('SUM(bottle_count)'))->whereIn('tc_bottle_id',$aryBottleCol2)
                        ->where('status','!=',0);
                }
            ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '<p class="mb-0">';

                $isNotImport = $data['tc_germin_transactions']->contains(function ($item) {
                    return $item['tc_worker_id'] != 99;
                });

                if (!$isNotImport) {
                    $el .= '<a href="#" class="btn-delete text-danger" data-url="'.route('germin-lists.rollbackImport', $data->id).'">Delete</a>
                            <span class="text-muted mx-1">-</span>';
                }
                $el .= '
                        <a class="text-primary" href="'.route('germin-lists.show',$data->id).'">Detail</a>
                ';
                $el .= "
                        <span class='text-muted mx-1'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='".route('germin-lists.comment',$data->id)."'>Comment</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('column1',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','germin_column1');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');
                $q = TcGerminBottle::select('id')->where('tc_init_id',$data->id)
                    ->where('status','!=',0)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcGerminBottle::usedBottle($value->id);
                }
                return $data->first_total_column1 - $usedBottle;
            })
            ->addColumn('column2',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','germin_column2');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');

                $q = TcGerminBottle::select('id')->where('tc_init_id',$data->id)
                    ->where('status','!=',0)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcGerminBottle::usedBottle($value->id);
                }
                return $data->first_total_column2 - $usedBottle;
            })
            ->addColumn('total_bottle_active',function($data){
                $q = TcGerminBottle::select('id')->where('tc_init_id',$data->id)
                    ->where('status','!=',0)
                    ->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcGerminBottle::usedBottle($value->id);
                }
                return $data->first_total - $usedBottle;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Germination List Data";
        $data['desc'] = "Display all germination bottle list";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['column1'] = TcBottleInit::where('keyword','germin_column1')->first()->getAttribute('column_name');
        $data['column2'] = TcBottleInit::where('keyword','germin_column2')->first()->getAttribute('column_name');
        return view('modules.germin_list.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $qCode = 'DATE_FORMAT(bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,bottle_date, 103)';
        }
        $list = ['germin_column1','germin_column2'];
        $q = TcBottleInit::whereIn('keyword',$list)->get();
        foreach ($q as $key => $value) {
            foreach ($value->tc_bottle_init_details as $key2 => $value2) {
                $bottleList[] = $value2->tc_bottle_id;
            }
        }

        $data = TcGerminBottle::select([
                'tc_germin_bottles.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->whereIn('tc_bottle_id',$bottleList)
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
                'tc_bottles'
            ])
        ;
        if($request->filter == 1 || !isset($request->filter)){
            $data = TcGerminBottle::select([
                'tc_germin_bottles.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->whereIn('tc_bottle_id',$bottleList)
            ->where('tc_init_id',$request->initId)
            ->where('status',1)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
                'tc_bottles'
            ])
        ;
        }
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('type',function($data){
                if(!is_null($data->tc_embryo_transfer_id)){
                    return "Direct";
                }

                if(!is_null($data->tc_matur_transfer_id)){
                    return "Liquid";
                }

                if(!is_null($data->tc_germin_transfer_id)){
                    return $data->type;
                }

            })
            ->addColumn('last_total',function($data){
                return $data->bottle_count - TcGerminBottle::usedBottle($data->id);
            })
            ->addColumn('import',function($data){
                $mark = null;
                if($data->tc_worker_id == 99){
                    $mark = '*';
                }
                return $mark;
            })
            ->addColumn('column1',function($data){
                $q = TcBottleInit::where('keyword','germin_column1')->with('tc_bottle_init_details')->get();
                $dataBottle = $q[0]->tc_bottle_init_details;
                $total = 0;
                foreach ($dataBottle as $key => $value) {
                    $bottleId = $value->tc_bottle_id;
                    if($bottleId == $data->tc_bottle_id){
                        $total = $total + $data->bottle_count;
                    }
                }
                return $total;
            })
            ->addColumn('column2',function($data){
                $q = TcBottleInit::where('keyword','germin_column2')->with('tc_bottle_init_details')->get();
                $dataBottle = $q[0]->tc_bottle_init_details;
                $total = 0;
                foreach ($dataBottle as $key => $value) {
                    $bottleId = $value->tc_bottle_id;
                    if($bottleId == $data->tc_bottle_id){
                        $total = $total + $data->bottle_count;
                    }
                }
                return $total;
            })
            ->rawColumns(['date_work_format'])
            ->smart(true)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $qCode = 'DATE_FORMAT(tc_germin_bottles.bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_germin_bottles.bottle_date, 103)';
        }
        $data = TcGerminTransaction::select([
                'tc_germin_transactions.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->leftJoin('tc_germin_bottles','tc_germin_bottles.id','=','tc_germin_transactions.tc_germin_bottle_id')
            ->whereHas('tc_germin_bottles',function(Builder $q){
                $q->where('status','!=',0);
            })
            ->where('tc_germin_transactions.tc_init_id',$request->initId)
            ->with([
                'tc_inits.tc_samples',
                'tc_germin_bottles',
                'tc_germin_bottles.tc_workers',
                'tc_workers:id,code',
            ])
        ;
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('obs_date',function($data){
                $return = $data->tc_germin_ob_id;
                if(!is_null($return)){
                    $return = TcGerminOb::where('id',$data->tc_germin_ob_id)->first()->getAttribute('ob_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->addColumn('transfer_date',function($data){
                $return = $data->tc_germin_transfer_id;
                if(!is_null($data->tc_germin_transfer_id)){
                    $return = TcGerminTransfer::where('id',$data->tc_germin_transfer_id)->first()->getAttribute('transfer_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }

    public function comment($id)
    {
        $data['title'] = "Germination Comments - Files - Images";
        $data['desc'] = "Manage data comment, file and image";
        $data['initId'] = $id;
        return view('modules.germin_list.comment', compact('data'));
    }

    public function dtComment(Request $request)
    {
        $data = TcGerminComment::select([
            'tc_germin_comments.*',
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
                        <a href="'.asset("storage/media/germin/file").'/'.$data->file.'">
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
                        <a href="'.asset("storage/media/germin/image").'/'.$data->image.'" target="_blank">
                        <img src="'.asset("storage/media/germin/image").'/'.$data->image.'" class="img-thumbnail" width="70">
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
            ($request->file('file'))->storeAs('public/media/germin/file', $dt['file']);
        }
        if ($request->hasFile('image')) {
            $dt['image'] = Str::uuid() . '.' . ($request->file('image'))->getClientOriginalExtension();
            ($request->file('image'))->storeAs('public/media/germin/image', $dt['image']);
        }

        TcGerminComment::create($dt);

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
        $data = TcGerminComment::find($request->id);
        Storage::delete('public/media/germin/file/'.$data->file);
        Storage::delete('public/media/germin/image/'.$data->image);
        TcGerminComment::find($request->id)->delete();
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
