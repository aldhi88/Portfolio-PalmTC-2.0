<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediumCreate;
use App\Http\Requests\MediumEdit;
use App\Models\TcMedium;
use Carbon\Carbon;
use Yajra\DataTables\CollectionDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class MediumController extends Controller
{
    public function index()
    {
        $data['title'] = "Medium Data";
        $data['desc'] = "Display all available Medium data";

        return view('modules.medium.index', compact('data'));
    }

    public function getHistory($id){
        $TcMedium = new TcMedium();
        $q = TcMedium::with('tc_medium_stocks','tc_medium_opname','tc_callus_transfer_stocks')
            ->where('id', $id);
        $data = $q->get()->first()->toArray();
        $mediumStocks = $data['tc_medium_stocks'];
        $mediumOpname = $data['tc_medium_opname'];
        // $mediumInit = $data['tc_init_of_mediums'];
        $callusTrans = $data['tc_callus_transfer_stocks'];
        if(count($mediumStocks) != 0){
            $collMediumStocks = collect();
            $lastStock = 0;
            foreach ($mediumStocks as $key => $value) {
                $lastTotal = $lastStock + $value['stock'];
                $collMediumStocks->push([
                    'type' => "Medium Stocks",
                    'created_at' => $value['created_at_short_format'],
                    'desc' => "Creating Stock",
                    'stock_in' => $value['stock'],
                    'stock_out' => 0,
                    'total' => $lastTotal,
                ]);

                // buat collection dari stock validate
                foreach ($mediumOpname as $key1 => $value2) {
                    if($value['id'] == $value2['tc_medium_stock_id']){
                        $lastTotal = $lastTotal + $value2['stock_in'] - $value2['stock_out'];
                        $collMediumStocks->push([
                            'type' => "Medium Opname",
                            'created_at' => $value2['created_at_short_format'],
                            'desc' => "Validation <br> <small>".$value2['desc']."</small>",
                            'stock_in' => $value2['stock_in'],
                            'stock_out' => $value2['stock_out'],
                            'total' => $lastTotal,
                        ]);
                    }
                }
                // foreach ($mediumInit as $key2 => $value3) {
                //     if($value['id'] == $value3['tc_medium_stock_id']){
                //         $lastTotal = $lastTotal - $value3['used_stock'];
                //         $collMediumStocks->push([
                //             'type' => "Initiation",
                //             'created_at' => $value3['created_at_short_format'],
                //             'desc' => "Initiation",
                //             'stock_in' => 0,
                //             'stock_out' => $value3['used_stock'],
                //             'total' => $lastTotal,
                //         ]);
                //     }
                // }
                // foreach ($callusTrans as $key3 => $value4) {
                //     if($value['id'] == $value4['tc_medium_stock_id']){
                //         $lastTotal = $lastTotal - $value4['stock_used'];
                //         $collMediumStocks->push([
                //             'type' => "Callus Transfer",
                //             'created_at' => Carbon::parse($value4['created_at'])->format('d/m/y'),
                //             'desc' => "Callus Transfer",
                //             'stock_in' => 0,
                //             'stock_out' => $value4['stock_used'],
                //             'total' => $lastTotal,
                //         ]);
                //     }
                // }

                $lastStock = $lastTotal;
                $data['histories'] = $collMediumStocks;
            }
        }else{
            $data['histories'] = NULL;
        }

        return view('modules.medium.history', compact('data'));
    }

    public function getData($id){
        $TcMedium = new TcMedium();
        $data = $TcMedium->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dt(){
        $data = TcMedium::with('tc_medium_stocks','tc_medium_opname')
            ->with(['tc_medium_stocks'])
            ->where('id', '!=', 99);

        return DataTables::of($data)
            ->addColumn('custom_name', function($data){
                $el = '<strong class="mt-0 font-size-14">'.$data->name.'</strong>';
                $attch['id'] = $data->id;
                $urlView = route('medium-stocks.indexFilter', $data->id);
                $urlAddStock = route('medium-stocks.createParam', $data->id);
                $el .= "
                    <p class='mb-0 font-size-14'>
                        <a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#editModal'>Edit</a>
                ";

                if($data->tc_medium_stocks->isEmpty()){
                    $el .= "
                            <span class='text-muted'>-</span>
                            <a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                        ";
                }

                // $el .= "
                //         <span class='text-muted'>-</span>
                //         <a class='text-primary' href='#' data-id='".$data->id."' data-toggle='modal' data-target='#historyModal'>History</a>
                //     ";
                $el .= "
                        <span class='text-muted'>-</span>
                        <a class='text-primary' href='".$urlView."'>View</a>
                    ";
                $el .= "
                        <span class='text-muted'>-</span>
                        <a class='text-primary' href='".$urlAddStock."'>Add Stock</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('stock', function($data){
                $currentStock = $data->tc_medium_stocks->sum('current_stock');
                return $currentStock;
            })
            ->rawColumns(['custom_name'])
            ->addIndexColumn()
            ->toJson();

    }

    public function store(MediumCreate $request)
    {
        $TcMedium = new TcMedium();
        $data = $request->except('_token');
        $TcMedium->inData($data);
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

    public function update(MediumEdit $request, $id)
    {
        $data = $request->except('_token', '_method','id');
        $TcMedium = new TcMedium();
        $TcMedium->upData($id, $data);
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
        $TcMedium = new TcMedium();
        $TcMedium->delData($id);
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
