<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaminarCreate;
use App\Http\Requests\LaminarEdit;
use App\Models\TcLaminar;
use DataTables;


class LaminarController extends Controller
{
    public function index()
    {
        $data['title'] = "Laminar Data";
        $data['desc'] = "Display all available Laminar data";

        return view('modules.laminar.index', compact('data'));
    }

    public function getData($id){
        $TcLaminar = new TcLaminar();
        $data = $TcLaminar->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    
    public function dt(){
        $data = TcLaminar::query()->where('id', '!=', 99);
    
        return DataTables::of($data)
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

    public function store(LaminarCreate $request)
    {
        $TcLaminar = new TcLaminar();
        $data = $request->except('_token');
        $TcLaminar->inData($data);
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

    public function update(LaminarEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcLaminar = new TcLaminar();
        $TcLaminar->upData($id, $data);
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
        $TcLaminar = new TcLaminar();
        $TcLaminar->delData($id);
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
