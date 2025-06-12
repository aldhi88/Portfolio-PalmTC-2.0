<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgarCreate;
use App\Http\Requests\AgarEdit;
use App\Models\TcAgar;
use DataTables;


class AgarController extends Controller
{
    public function index()
    {
        $data['title'] = "Agar Rose Data";
        $data['desc'] = "Display all available Agar Rose data";

        return view('modules.agar.index', compact('data'));
    }

    public function getData($id){
        $TcAgar = new TcAgar();
        $data = $TcAgar->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $data = TcAgar::query()->where('id', '!=', 99);
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

    public function store(AgarCreate $request)
    {
        $TcAgar = new TcAgar();
        $data = $request->except('_token');
        $TcAgar->inData($data);
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

    public function update(AgarEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcAgar = new TcAgar();
        $TcAgar->upData($id, $data);
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
        $TcAgar = new TcAgar();
        $TcAgar->delData($id);
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
