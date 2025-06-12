<?php

namespace App\Http\Controllers;

use App\Http\Requests\BottleCreate;
use App\Http\Requests\BottleEdit;
use App\Models\TcBottle;
use DataTables;


class BottleController extends Controller
{
    public function getData($id){
        $TcBottle = new TcBottle();
        $data = $TcBottle->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $data = TcBottle::query()->where('id', '!=', 99);
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
    public function index()
    {
        $data['title'] = "Bottle Data";
        $data['desc'] = "Display all available Bottle data";

        return view('modules.bottle.index', compact('data'));
    }
    public function create()
    {
        //
    }

    public function store(BottleCreate $request)
    {
        $TcBottle = new TcBottle();
        $data = $request->except('_token');
        $TcBottle->inData($data);
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

    public function update(BottleEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcBottle = new TcBottle();
        $TcBottle->upData($id, $data);
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
        $TcBottle = new TcBottle();
        $TcBottle->delData($id);
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
