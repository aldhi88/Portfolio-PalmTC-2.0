<?php

namespace App\Http\Controllers;

use App\Models\TcBottleInit;
use App\Models\TcBottleInitDetail;
use App\Models\TcInit;
use App\Models\TcRootingBottle;
use App\Models\TcRootingComment;
use App\Models\TcRootingOb;
use App\Models\TcRootingTransaction;
use App\Models\TcRootingTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RootingListController extends Controller
{
    public function index()
    {
        $data['title'] = "Rooting Per Sample";
        $data['desc'] = "Display all available data";
        $data['column1'] = TcBottleInit::where('keyword','rooting_column1')->first()->getAttribute('column_name');
        $data['column2'] = TcBottleInit::where('keyword','rooting_column2')->first()->getAttribute('column_name');
        return view('modules.rooting_list.index',compact('data'));
    }
    public function dt()
    {
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q){
                $q->where('keyword','rooting_column1');
            })->get()->toArray();
        $aryBottleCol1 = array_column($q, 'tc_bottle_id');
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q){
                $q->where('keyword','rooting_column2');
            })->get()->toArray();
        $aryBottleCol2 = array_column($q, 'tc_bottle_id');
        $data = TcInit::select(['tc_inits.*'])
            ->whereHas('tc_rooting_bottles')
            ->with([
                'tc_samples',
            ])
            ->withCount([
                'tc_rooting_bottles as first_total' => function($q){
                    $q->select(DB::raw('SUM(bottle_count)'));
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_leaf' => function($q){
                    $q->select(DB::raw('SUM(leaf_count)'));
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_column1' => function($q) use($aryBottleCol1){
                    $q->select(DB::raw('SUM(bottle_count)'))->whereIn('tc_bottle_id',$aryBottleCol1)
                        ->where('status','!=',0)->where('type',1);
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_explant' => function($q) use($aryBottleCol1){
                    $q->select(DB::raw('SUM(leaf_count)'))->whereIn('tc_bottle_id',$aryBottleCol1)
                        ->where('status','!=',0)->where('type',1);
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_column2' => function($q) use($aryBottleCol2){
                    $q->select(DB::raw('SUM(bottle_count)'))->whereIn('tc_bottle_id',$aryBottleCol2)
                        ->where('status','!=',0);
                }
            ])
        ;
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('rooting-lists.show',$data->id).'">Detail</a>
                ';
                $el .= "
                        <span class='text-muted mx-1'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='".route('rooting-lists.comment',$data->id)."'>Comment</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('column1',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','rooting_column1');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get()
                    ->where('status','!=',0)->where('type',1)
                ;
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottle($value->id);
                }
                return $data->first_total_column1 - $usedBottle;
            })
            ->addColumn('explant1',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','rooting_column1');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get()
                    ->where('status','!=',0)->where('type',1);
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottleLeaf($value->id);
                }
                return $data->first_total_explant - $usedBottle;
            })
            ->addColumn('column2',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','rooting_column2');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');

                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get()
                    ->where('status','!=',0)->where('type',2);
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottle($value->id);
                }
                return $data->first_total_column2 - $usedBottle;
            })
            ->addColumn('total_bottle_active',function($data){
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottle($value->id);
                }
                return $data->first_total - $usedBottle;
            })
            ->addColumn('total_leaf_active',function($data){
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottleLeaf($value->id);
                }
                return $data->first_total_leaf - $usedBottle;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Rooting List Data";
        $data['desc'] = "Display all rooting bottle list";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['column1'] = TcBottleInit::where('keyword','rooting_column1')->first()->getAttribute('column_name');
        $data['column2'] = TcBottleInit::where('keyword','rooting_column2')->first()->getAttribute('column_name');
        return view('modules.rooting_list.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $qCode = 'DATE_FORMAT(bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,bottle_date, 103)';
        }
        $list = ['rooting_column1','rooting_column2'];
        $q = TcBottleInit::whereIn('keyword',$list)->get();
        foreach ($q as $key => $value) {
            foreach ($value->tc_bottle_init_details as $key2 => $value2) {
                $bottleList[] = $value2->tc_bottle_id;
            }
        }
        $data = TcRootingBottle::select([
                'tc_rooting_bottles.*',
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
            $data = TcRootingBottle::select([
                    'tc_rooting_bottles.*',
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
            ->addColumn('last_total',function($data){
                return $data->bottle_count - TcRootingBottle::usedBottle($data->id);
            })
            ->addColumn('last_total_leaf',function($data){
                return $data->leaf_count - TcRootingBottle::usedBottleLeaf($data->id);
            })
            ->addColumn('import',function($data){
                $mark = null;
                if($data->tc_worker_id == 99){
                    $mark = '*';
                }
                return $mark;
            })
            ->addColumn('column1',function($data){
                $q = TcBottleInit::where('keyword','rooting_column1')->with('tc_bottle_init_details')->get();
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
            ->addColumn('explant1',function($data){
                $q = TcBottleInit::where('keyword','rooting_column1')->with('tc_bottle_init_details')->get();
                $dataBottle = $q[0]->tc_bottle_init_details;
                $total = 0;
                foreach ($dataBottle as $key => $value) {
                    $bottleId = $value->tc_bottle_id;
                    if($bottleId == $data->tc_bottle_id){
                        $total = $total + $data->leaf_count;
                    }
                }
                return $total;
            })
            ->addColumn('column2',function($data){
                $q = TcBottleInit::where('keyword','rooting_column2')->with('tc_bottle_init_details')->get();
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
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $qCode = 'DATE_FORMAT(tc_rooting_bottles.bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_rooting_bottles.bottle_date, 103)';
        }
        $data = TcRootingTransaction::select([
                'tc_rooting_transactions.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->leftJoin('tc_rooting_bottles','tc_rooting_bottles.id','=','tc_rooting_transactions.tc_rooting_bottle_id')
            ->with([
                'tc_inits.tc_samples',
                'tc_rooting_bottles',
                'tc_rooting_bottles.tc_workers',
                'tc_workers:id,code',
            ])
            ->where('tc_rooting_transactions.tc_init_id',$request->initId)
            ->whereHas('tc_rooting_bottles',function(Builder $q){
                $q->where('status','!=',0);
            })
        ;
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('obs_date',function($data){
                $return = $data->tc_rooting_ob_id;
                if(!is_null($return)){
                    $return = TcRootingOb::where('id',$data->tc_rooting_ob_id)->first()->getAttribute('ob_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->addColumn('transfer_date',function($data){
                $return = $data->tc_rooting_transfer_id;
                if(!is_null($data->tc_rooting_transfer_id)){
                    $return = TcRootingTransfer::where('id',$data->tc_rooting_transfer_id)->first()->getAttribute('transfer_date');
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
        $data['title'] = "Rooting Comments - Files - Images";
        $data['desc'] = "Manage data comment, file and image";
        $data['initId'] = $id;
        return view('modules.rooting_list.comment', compact('data'));
    }

    public function dtComment(Request $request)
    {
        $data = TcRootingComment::select([
            'tc_rooting_comments.*',
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
                        <a href="'.asset("storage/media/rooting/file").'/'.$data->file.'">
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
                        <a href="'.asset("storage/media/rooting/image").'/'.$data->image.'" target="_blank">
                        <img src="'.asset("storage/media/rooting/image").'/'.$data->image.'" class="img-thumbnail" width="70">
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
            ($request->file('file'))->storeAs('public/media/rooting/file', $dt['file']);
        }
        if ($request->hasFile('image')) {
            $dt['image'] = Str::uuid() . '.' . ($request->file('image'))->getClientOriginalExtension();
            ($request->file('image'))->storeAs('public/media/rooting/image', $dt['image']);
        }

        TcRootingComment::create($dt);

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
        $data = TcRootingComment::find($request->id);
        Storage::delete('public/media/rooting/file/'.$data->file);
        Storage::delete('public/media/rooting/image/'.$data->image);
        TcRootingComment::find($request->id)->delete();
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
