<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlantationCreate;
use App\Http\Requests\PlantationEdit;
use App\Models\TcPlantation;
use DataTables;

class PlantationController extends Controller
{
    public function index()
    {
        $data['title'] = "Plantation Data";
        $data['desc'] = "Display all available Plantation";

        return view('modules.plantation.index', compact('data'));
    }

    public function getData($id){
        $data = TcPlantation::where('id',$id)->get()->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $data = TcPlantation::query()->where('id', '!=', 99);
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

    public function store(PlantationCreate $request)
    {
        $data = $request->except('_token');
        TcPlantation::create($data);
        return alert(1,null,null);
    }

    public function update(PlantationEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        TcPlantation::where('id',$id)->update($data);
        return alert(1,null,null);
    }
    
    public function destroy($id)
    {
        TcPlantation::where('id',$id)->delete();
        return alert(1,null,null);
    }
}
