<?php

namespace App\Http\Controllers;

use App\Http\Requests\BottleInitUpdate;
use App\Models\TcBottle;
use App\Models\TcBottleInit;
use App\Models\TcBottleInitDetail;
use Illuminate\Http\Request;
use DataTables;

class BottleInitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = "Bottle Bottle Initiation";
        $data['desc'] = "Bottle initiation for column report";
        return view('modules.bottle_init.index', compact('data'));
    }
    public function dtIndex(Request $request)
    {
        $data = TcBottleInit::select('tc_bottle_inits.*')
            ->with([
                'tc_bottle_init_details',
                'tc_bottle_init_details.tc_bottles'
            ])    
        ;

        return DataTables::of($data)
            ->addColumn('name_custom',function($data){
                $el = '<strong class="mt-0 font-size-14">'.$data->column_name.'</strong>';
                $attch['id'] = $data->id;
                $attch['name'] = $data->column_name;
                $el .= "
                    <p class='mb-0 font-size-14'>
                        <a class='text-primary' data-id='".$data->id."' data-json='".json_encode($attch)."' href='#' data-toggle='modal' data-target='#editModal'>Edit</a>
                ";
                $el .= "
                        <span class='text-muted mx-1'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#bottleListModal'>Add Bottle</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('bottle_list',function($data){
                $return = null;
                foreach ($data->tc_bottle_init_details as $key => $value) {
                    $return .= $value->tc_bottles->code.'<br>';
                }
                return $return;
            })
            ->rawColumns(['name_custom','bottle_list'])
            ->smart(false)->toJson();
    }

    public function getDataBottle(Request $request){
        $data['bottles'] = TcBottle::all();
        $data['bottleInit'] = TcBottleInit::select('id','column_name')
            ->where('id',$request->id)->first();
        $data['bottleList'] = TcBottleInitDetail::select('tc_bottle_id')
            ->where('tc_bottle_init_id',$data['bottleInit']->id)->get()->toArray();
        foreach ($data['bottleList'] as $key => $value) {
            $data['bottleList'][] = $value['tc_bottle_id'];
        }
        return view('modules.bottle_init.bottle_list',compact('data'));
    }
    public function actionChecked(Request $request)
    {
        if($request->action == 'in'){
            $data = $request->except('action');
            TcBottleInitDetail::create($data);
        }else{
            TcBottleInitDetail::where('tc_bottle_init_id',$request->tc_bottle_init_id)
                ->where('tc_bottle_id',$request->tc_bottle_id)
                ->delete();
        }
    }

    public function store(BottleInitColumnStore $request)
    {
        $data = $request->except('_token');
        TcBottleInit::create($data);
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

    public function update(BottleInitUpdate $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        TcBottleInit::where('id',$id)->update($data);
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
        TcBottleInit::where('id',$id)->delete();
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
