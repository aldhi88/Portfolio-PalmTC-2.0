<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkerCreate;
use App\Http\Requests\WorkerEdit;
use App\Models\TcWorker;
use Carbon\Carbon;
use DataTables;
use DB;

class WorkerController extends Controller
{
    public function getData($id){
        $TcWorker = new TcWorker();
        $data = $TcWorker->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){

        $data = TcWorker::select([
                'tc_workers.*',
                DB::raw('convert(varchar,date_of_birth, 103) as date_of_birth_format'), //note*
            ])
            ->where('id', '!=', 99);

        ;
        return DataTables::of($data)
            ->addColumn('created_at_custom',function($data){
                $return = Carbon::parse($data->created_at)->format('H:i:s d/m/Y');
                return '<small class="d-block" style="width:60px !important">'.$return.'</small>';
            })
            ->addColumn('custom_status', function($data){
                return $data->status==0?'<span class="badge badge-secondary">Inactive</span>':'<span class="badge badge-primary">Active</span>';
            })
            ->addColumn('custom_no_pekerja', function($data){
                $el = '<strong><p class="my-0">'.$data->no_pekerja.'</p></strong>';
                $el .= "
                    <a class='text-primary fs-13' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#editModal'>Edit</a>
                ";

                $el .= "
                    - <a class='text-danger fs-13' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                ";
                return $el;
            })
            ->filterColumn('date_of_birth_format', function($query, $keyword) {
                $sql = 'convert(varchar,date_of_birth, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->rawColumns(['custom_no_pekerja', 'custom_status','created_at_custom'])
            ->toJson();

    }
    public function index()
    {
        $data['title'] = "Worker Data";
        $data['desc'] = "Display all available Worker data";

        return view('modules.worker.index', compact('data'));
    }

    public function store(WorkerCreate $request)
    {
        $TcWorker = new TcWorker();
        $data = $request->except('_token');
        $TcWorker->inData($data);
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

    public function update(WorkerEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcWorker = new TcWorker();
        $TcWorker->upData($id, $data);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data updated successfully.',
            ],
        ]);
    }

    public function destroy($id)
    {
        $TcWorker = new TcWorker();
        $TcWorker->delData($id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data deleted successfully.',
            ],
        ]);
    }
}
