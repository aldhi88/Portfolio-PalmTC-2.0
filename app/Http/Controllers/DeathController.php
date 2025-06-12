<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeathCreate;
use App\Http\Requests\DeathEdit;
use App\Models\TcDeath;
use DataTables;
use Illuminate\Http\Request;

class DeathController extends Controller
{
    public function index()
    {
        $data['title'] = "Death Data";
        $data['desc'] = "Display all available Death";

        return view('modules.death.index', compact('data'));
    }

    public function getData($id){
        $data = TcDeath::where('id',$id)->get()->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $data = TcDeath::query()->where('id', '!=', 99);
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

    public function store(DeathCreate $request)
    {
        $data = $request->except('_token');
        TcDeath::create($data);
        return alert(1,null,null);
    }

    public function update(DeathEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        TcDeath::where('id',$id)->update($data);
        return alert(1,null,null);
    }
    
    public function destroy($id)
    {
        TcDeath::where('id',$id)->delete();
        return alert(1,null,null);
    }
}
