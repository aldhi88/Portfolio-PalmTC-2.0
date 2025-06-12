<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediumOpnameCreate;
use App\Http\Requests\MediumOpnameEdit;
use App\Models\TcMedium;
use App\Models\TcMediumOpname;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class MediumOpnameController extends Controller
{
    public function index()
    {
        $data['title'] = "Medium Stock Validate Data";
        $data['desc'] = "Display all available Medium Stock Validate";

        return view('modules.medium_opname.index', compact('data'));
    }

    public function getData($id){
        $TcMediumOpname = new TcMediumOpname();
        $data = $TcMediumOpname->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $TcMediumOpname = new TcMediumOpname();
        $data = $TcMediumOpname->selDataDt();
        return Datatables::of($data)
            ->addColumn('custom_created_at', function($data){
                $el = '<strong class="mt-0 font-size-14">'.$data->created_at_long_format.'</strong>';
                $el .= "
                    <p class='mb-0 font-size-14'>
                        <a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#editModal'>Edit</a>
                ";

                $el .= "
                        <span class='text-muted'>-</span>
                        <a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('medium_name', function($data){
                $TcMedium = new TcMedium();
                $dataMedium = $TcMedium->selByCol('id', $data->tc_medium_stocks->tc_medium_id)->first();
                return $dataMedium->name.'<br>#'.$data->tc_medium_stock_id;
            })
            ->rawColumns(['medium_name','custom_created_at'])
            ->addIndexColumn()
            ->toJson();

    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MediumOpnameCreate $request)
    {
        $TcMediumOpname = new TcMediumOpname();
        $data = $request->except('_token');
        $TcMediumOpname->inData($data);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MediumOpnameEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcMediumOpname = new TcMediumOpname();
        $TcMediumOpname->upData($id, $data);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $TcMediumOpname = new TcMediumOpname();
        $TcMediumOpname->delData($id);
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
