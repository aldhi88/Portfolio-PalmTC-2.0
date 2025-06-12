<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmbryoBottleSubtractionCreate;
use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoBottleSubtraction;
use App\Models\TcEmbryoComment;
use App\Models\TcEmbryoList;
use App\Models\TcEmbryoOb;
use App\Models\TcEmbryoTransfer;
use App\Models\TcInit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


use function PHPUnit\Framework\isNull;

class EmbryoListController extends Controller
{
    public function index()
    {
        $data['title'] = "Embryogenesis Per Sample";
        $data['desc'] = "Display all embryogenesis";
        return view('modules.embryo_list.index',compact('data'));
    }
    public function dt()
    {
        $data = TcInit::select(['tc_inits.id','tc_inits.tc_sample_id'])
            ->whereHas('tc_embryo_bottles')
            ->with([
                'tc_samples:id,sample_number,program',
                'tc_embryo_bottles:tc_worker_id'
            ])
            ->withCount([
                'tc_embryo_bottles as first_total' => function($q){
                    $q->select(DB::raw('SUM(number_of_bottle) as first_total'))->where('status','!=',0);
                }
            ])
        ;
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('embryo-lists.show',$data->id).'">Detail</a>
                ';
                $el .= "
                        <span class='text-muted mx-1'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='".route('embryo-lists.comment',$data->id)."'>Comment</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('total_bottle_active',function($data){
                if (is_null($data->first_total)) {
                    return 0;
                }
                $q = TcEmbryoBottle::select('id')
                    ->where('tc_init_id',$data->id)
                    ->where('status','!=',0)
                    ->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcEmbryoBottle::usedBottle($value->id);
                }
                return $data->first_total - $usedBottle;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Embryogenesis List Data";
        $data['desc'] = "Display all embryogenesis bottle list";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        return view('modules.embryo_list.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcEmbryoBottle::select([
                'tc_embryo_bottles.*',
                DB::raw('convert(varchar,bottle_date, 103) as bottle_date_format')
            ])
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
            ])
        ;

        if($request->filter == 1 || !isset($request->filter)){
            $data->where('status',1);
        }

        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,bottle_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('import',function($data){
                $mark = null;
                if($data->tc_worker_id == 99){
                    $mark = '*';
                }
                return $mark;
            })
            ->addColumn('last_total',function($data){
                return $data->number_of_bottle - TcEmbryoBottle::usedBottle($data->id);
            })
            ->rawColumns(['date_work_format'])
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $qCode = 'DATE_FORMAT(tc_embryo_bottles.bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_embryo_bottles.bottle_date, 103)';
        }
        $data = TcEmbryoList::select([
                'tc_embryo_lists.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->leftJoin('tc_embryo_bottles','tc_embryo_bottles.id','=','tc_embryo_lists.tc_embryo_bottle_id')
            ->with([
                'tc_inits.tc_samples',
                'tc_embryo_bottles',
                'tc_embryo_bottles.tc_workers',
                'tc_workers:id,code',
            ])
            ->where('tc_embryo_lists.tc_init_id',$request->initId)
            ->whereHas('tc_embryo_bottles',function(Builder $q){
                $q->where('status','!=',0);
            })
        ;
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('obs_date',function($data){
                $return = $data->tc_embryo_ob_id;
                if(!is_null($data->tc_embryo_ob_id)){
                    $return = TcEmbryoOb::where('id',$data->tc_embryo_ob_id)
                        ->first()
                        ->getAttribute('work_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->addColumn('transfer_date',function($data){
                $return = $data->tc_embryo_transfer_id;
                if(!is_null($data->tc_embryo_transfer_id)){
                    $return = TcEmbryoTransfer::where('id',$data->tc_embryo_transfer_id)
                        ->first()
                        ->getAttribute('transfer_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }
    public function showSubtraction(Request $request)
    {
        $data['subtractions'] = TcEmbryoBottleSubtraction::where('tc_embryo_bottle_id',$request->id)
            ->get();
        return view('modules.embryo_list.view_subtraction',compact('data'));
    }
    public function storeSubtraction(EmbryoBottleSubtractionCreate $request)
    {
        $data = $request->except('_token');
        TcEmbryoBottleSubtraction::create($data);
        TcEmbryoBottle::where('id',$request->tc_embryo_bottle_id)
            ->decrement('number_of_bottle',$request->bottle_count);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, total bottle has been changed.',
            ],
        ]);
    }
    public function destroySubtraction(Request $request)
    {
        $q = TcEmbryoBottleSubtraction::where('id',$request->id)
            ->first();
        $bottleCount = $q->bottle_count;
        $bottleId = $q->tc_embryo_bottle_id;
        TcEmbryoBottleSubtraction::where('id',$request->id)
            ->forceDelete();
        TcEmbryoBottle::where('id',$bottleId)
            ->increment('number_of_bottle',$bottleCount);

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area-delSubtraction',
                'msg' => 'Success, data has been delete.',
                'id' => $bottleId
            ],
        ]);
    }

    public function comment($id)
    {
        $data['title'] = "Embryogenesis Comments - Files - Images";
        $data['desc'] = "Manage data comment, file and image";
        $data['initId'] = $id;
        return view('modules.embryo_list.comment', compact('data'));
    }

    public function dtComment(Request $request)
    {
        $data = TcEmbryoComment::select([
            'tc_embryo_comments.*',
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
                        <a href="'.asset("storage/media/embryo/file").'/'.$data->file.'">
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
                        <a href="'.asset("storage/media/embryo/image").'/'.$data->image.'" target="_blank">
                        <img src="'.asset("storage/media/embryo/image").'/'.$data->image.'" class="img-thumbnail" width="70">
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
            ($request->file('file'))->storeAs('public/media/embryo/file', $dt['file']);
        }
        if ($request->hasFile('image')) {
            $dt['image'] = Str::uuid() . '.' . ($request->file('image'))->getClientOriginalExtension();
            ($request->file('image'))->storeAs('public/media/embryo/image', $dt['image']);
        }

        TcEmbryoComment::create($dt);

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
        $data = TcEmbryoComment::find($request->id);
        Storage::delete('public/media/embryo/file/'.$data->file);
        Storage::delete('public/media/embryo/image/'.$data->image);
        TcEmbryoComment::find($request->id)->delete();
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
