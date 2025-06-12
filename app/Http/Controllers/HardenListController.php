<?php

namespace App\Http\Controllers;

use App\Models\TcHarden;
use App\Models\TcHardenComment;
use App\Models\TcHardenTree;
use App\Models\TcInit;
use App\Models\TcSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Database\Eloquent\Builder;

class HardenListController extends Controller
{
    public function index()
    {
        $data['title'] = "Hardening Per Sample";
        $data['desc'] = "Display all available data";
        return view('modules.harden_list.index',compact('data'));
    }

    public function dt()
    {
        $data = TcInit::select([
                'tc_inits.id',
                'tc_inits.tc_sample_id',
            ])
            ->whereHas('tc_hardens')
            ->with([
                'tc_samples:id,sample_number,program',
                'tc_hardens:id,tc_init_id',
                'tc_hardens.tc_harden_trees' => function($q){
                    $q->select('id','tc_harden_id');
                },
            ])
            ->withCount([
                'tc_harden_trees as total_data' => function($q){
                    $q->where('tc_harden_trees.status','!=',0);
                }
            ])
            ->withCount([
                'tc_hardens as total_date' => function($q){
                    $q->where('tc_hardens.status',1);
                }
            ])
            ->withCount([
                'tc_harden_trees as total_active' => function($q){
                    $q->where('tc_harden_trees.status',1);
                }
            ])
            ->withCount([
                'tc_harden_ob_details as total_death' => function($q){
                    $q->where('is_death',1)
                        ->whereHas('tc_harden_trees', function($q2){
                            $q2->whereHas('tc_hardens',function($q3){
                                $q3->where('status',1);
                            });
                        });
                }
            ])
            ->withCount([
                'tc_harden_ob_details as total_transfer' => function($q){
                    $q->where('is_transfer',1)
                        ->whereHas('tc_harden_trees', function($q2){
                            $q2->whereHas('tc_hardens',function($q3){
                                $q3->where('status',1);
                            });
                        });
                }
            ])
        ;

        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('harden-lists.show',$data->id).'">Detail</a>
                ';
                $el .= "
                        <span class='text-muted mx-1'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='".route('harden-lists.comment',$data->id)."'>Comment</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Hardening List Data";
        $data['desc'] = "Display all matur bottle list";
        $data['initId'] = $id;
        $data['sampleNumber'] = TcSample::select('id','sample_number')
            ->whereHas('tc_inits', function(Builder $q) use($id){
                $q->where('id',$id);
            })
            ->first()->getAttribute('sample_number_display');
        return view('modules.harden_list.show',compact('data'));
    }

    public function dtShow(Request $request)
    {
        $data = TcHarden::select([
                'tc_hardens.*',
                DB::raw('convert(varchar,tree_date, 103) as tree_date_format')
            ])
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
            ])
            ->withCount(['tc_harden_trees as total_data'])
            ->withCount(['tc_harden_trees as total_active' => function($q){
                $q->where('status',1);
            }])
            ->withCount([
                'tc_harden_ob_details as total_death' => function($q){
                    $q->where('is_death',1);
                }
            ])
            ->withCount([
                'tc_harden_ob_details as total_transfer' => function($q){
                    $q->where('is_transfer',1);
                }
            ])
        ;
        if($request->filter == 1 || !isset($request->filter)){
            $data->where('status','!=',0);
        }

        return DataTables::of($data)
            ->filterColumn('tree_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,tree_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('import',function($data){
                $mark = null;
                if($data->tc_worker_id == 99){
                    $mark = '*';
                }
                return $mark;
            })
            ->addColumn('tree_date_action',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tree_date_format.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary detail" data-date="'.$data->tree_date_format.'" data-id="'.$data->id.'" href="#'.$data->id.'">Detail</a>
                ';
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['tree_date_action'])
            ->smart(false)->toJson();
    }

    public function dtShow2(Request $request)
    {
        $hardenId = $request->filter;
        $data = TcHardenTree::select([
                'tc_harden_trees.*',
                DB::raw('convert(varchar,tc_hardens.tree_date, 103) as tree_date_format'),
            ])
            ->where('tc_harden_id',$hardenId)
            ->where('tc_harden_trees.status','<',2)
            ->leftJoin('tc_hardens','tc_hardens.id','=','tc_harden_trees.tc_harden_id');
        return DataTables::of($data)
            ->addColumn('status_format',function($data){
                if($data->status==1){
                    $badge = "on text-primary";
                }else{
                    $badge = "off text-secondary";
                }
                return '<i data-id="'.$data->id.'" data-status="'.$data->status.'" class="switch fas fa-toggle-'.$badge.'"></i>';
            })
            ->addColumn('skor_akar',function($data){
                $el = '<input type="number" class="w-100 skor-akar text-center" value="'.$data->skor_akar.'" name="skor_akar_'.$data->id.'" data-id="'.$data->id.'">';
                return $el;
            })
            ->rawColumns(['status_format','skor_akar'])
            ->smart(false)->toJson();
    }

    public function changeStatus(Request $request)
    {
        TcHardenTree::where("id",$request->id)->update(["status" => $request->status]);
        return response()->json([
            'status' => 'success',
            'data' => [
                'status' => $request->status,
                'id' => $request->id,
            ],
        ]);
    }

    public function changeSkorAkar(Request $request)
    {
        TcHardenTree::where('id',$request->id)->update(['skor_akar' => $request->skor_akar]);
    }

    public function comment($id)
    {
        $data['title'] = "Hardening Comments - Files - Images";
        $data['desc'] = "Manage data comment, file and image";
        $data['initId'] = $id;
        return view('modules.harden_list.comment', compact('data'));
    }

    public function dtComment(Request $request)
    {
        $data = TcHardenComment::select([
            'tc_harden_comments.*',
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
                        <a href="'.asset("storage/media/harden/file").'/'.$data->file.'">
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
                        <a href="'.asset("storage/media/harden/image").'/'.$data->image.'" target="_blank">
                        <img src="'.asset("storage/media/harden/image").'/'.$data->image.'" class="img-thumbnail" width="70">
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
            ($request->file('file'))->storeAs('public/media/harden/file', $dt['file']);
        }
        if ($request->hasFile('image')) {
            $dt['image'] = Str::uuid() . '.' . ($request->file('image'))->getClientOriginalExtension();
            ($request->file('image'))->storeAs('public/media/harden/image', $dt['image']);
        }

        TcHardenComment::create($dt);

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
        $data = TcHardenComment::find($request->id);
        Storage::delete('public/media/harden/file/'.$data->file);
        Storage::delete('public/media/harden/image/'.$data->image);
        TcHardenComment::find($request->id)->delete();
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
