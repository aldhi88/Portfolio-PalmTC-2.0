<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContaminationCreate;
use App\Http\Requests\ContaminationEdit;
use App\Models\TcContamination;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ContaminationController extends Controller
{
    public function index()
    {
        $data['title'] = "Contamination Data";
        $data['desc'] = "Display all available Contamination";

        return view('modules.contamination.index', compact('data'));
    }

    public function getData($id){
        $TcContamination = new TcContamination();
        $data = $TcContamination->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $data = TcContamination::query()->where('id', '!=', 99);
        return Datatables::of($data)
            ->editColumn('custom_name', function($data){
                $el = '<strong class="mt-0 font-size-14">'.$data->name.'</strong>';
                $attch['id'] = $data->id;
                $el .= "
                    <p class='mb-0 font-size-14'>
                        <a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#editModal'>Edit</a>
                ";

                $el .= "
                        <span class='text-muted mx-1'>-</span>
                        <a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['custom_name'])
            ->addIndexColumn()
            ->toJson();

    }

    public function store(ContaminationCreate $request)
    {
        $TcContamination = new TcContamination();
        $data = $request->except('_token');
        $TcContamination->inData($data);
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

    public function update(ContaminationEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcContamination = new TcContamination();
        $TcContamination->upData($id, $data);
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
        $TcContamination = new TcContamination();
        $TcContamination->delData($id);
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
