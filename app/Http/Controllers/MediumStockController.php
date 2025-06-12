<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediumStockCreate;
use App\Http\Requests\MediumStockEdit;
use App\Models\TcAgar;
use App\Models\TcMedium;
use App\Models\TcMediumStock;
use App\Models\TcBottle;
use App\Models\TcCallusTransferStock;
use App\Models\TcEmbryoTransferStock;
use App\Models\TcGerminTransferStock;
use App\Models\TcInit;
use App\Models\TcLiquidTransferStock;
use App\Models\TcMaturTransferStock;
use App\Models\TcRootingTransferStock;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use DataTables;


use function PHPUnit\Framework\isNull;

class MediumStockController extends Controller
{

    public function getData($id){
        $TcMediumStock = new TcMediumStock();
        $data = $TcMediumStock->selByCol('id', $id);
        $data[0]->format_created_at = Carbon::parse($data[0]->created_at)->format('d M Y');
        $stock = $data[0]->stock;
        $stockIn = $data[0]['tc_medium_opname']->sum('stock_in');
        $stockOut = $data[0]['tc_medium_opname']->sum('stock_out');
        $data[0]->last_stock = $stock + $stockIn - $stockOut;
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }

    public function getHistory($id){
        $tcMediumStock = new TcMediumStock();
        $histories = $tcMediumStock->dataHistory($id)->first();
        $data['histories'] = $histories;
        $stock = $data['histories']->stock;
        $data['tc_medium_opname'] = $histories->tc_medium_opname->toArray();
        foreach ($data['tc_medium_opname'] as $key => $value) {
            $lastStock = $stock + $value['stock_in'] - $value['stock_out'];
            $data['tc_medium_opname'][$key]['total'] = $lastStock;
            $stock = $lastStock;
        }
        $data['init_stocks'] = TcInit::whereHas('tc_init_bottles',function(Builder $query) use($id){
            $query->where('status',1)
                ->where('tc_medium_stock_id',$id);
        })
        ->withCount(['tc_init_bottles' => function($q) use($id){
                $q->where('status',1)
                    ->where('tc_medium_stock_id',$id);
            }
        ])
        ->get()
        ->toArray();
        foreach ($data['init_stocks'] as $key => $value) {
            $lastStock = $stock - $value['tc_init_bottles_count'];
            $data['init_stocks'][$key]['total'] = $lastStock;
            $data['init_stocks'][$key]['date_format'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $stock = $lastStock;
        }
        $data['callus_trans'] = TcCallusTransferStock::where('tc_medium_stock_id',$id)->get()->toArray();
        foreach ($data['callus_trans'] as $key => $value) {
            $lastStock = $stock - $value['stock_used'];
            $data['callus_trans'][$key]['created_at'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $data['callus_trans'][$key]['total'] = $lastStock;
        }

        $data['embryo_trans'] = TcEmbryoTransferStock::where('tc_medium_stock_id',$id)->get()->toArray();
        foreach ($data['embryo_trans'] as $key => $value) {
            $lastStock = $stock - $value['used_stock'];
            $data['embryo_trans'][$key]['created_at'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $data['embryo_trans'][$key]['total'] = $lastStock;
            $stock = $lastStock;
        }

        $data['liquid_trans'] = TcLiquidTransferStock::where('tc_medium_stock_id',$id)->get()->toArray();
        foreach ($data['liquid_trans'] as $key => $value) {
            $lastStock = $stock - $value['used_stock'];
            $data['liquid_trans'][$key]['created_at'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $data['liquid_trans'][$key]['total'] = $lastStock;
            $stock = $lastStock;
        }

        $data['matur_trans'] = TcMaturTransferStock::where('tc_medium_stock_id',$id)->get()->toArray();
        foreach ($data['matur_trans'] as $key => $value) {
            $lastStock = $stock - $value['used_stock'];
            $data['matur_trans'][$key]['created_at'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $data['matur_trans'][$key]['total'] = $lastStock;
            $stock = $lastStock;
        }

        $data['germin_trans'] = TcGerminTransferStock::where('tc_medium_stock_id',$id)->get()->toArray();
        foreach ($data['germin_trans'] as $key => $value) {
            $lastStock = $stock - $value['used_stock'];
            $data['germin_trans'][$key]['created_at'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $data['germin_trans'][$key]['total'] = $lastStock;
            $stock = $lastStock;
        }

        $data['rooting_trans'] = TcRootingTransferStock::where('tc_medium_stock_id',$id)->get()->toArray();
        foreach ($data['rooting_trans'] as $key => $value) {
            $lastStock = $stock - $value['used_stock'];
            $data['rooting_trans'][$key]['created_at'] = Carbon::parse($value['created_at'])->format('d/m/y');
            $data['rooting_trans'][$key]['total'] = $lastStock;
            $stock = $lastStock;
        }

        return view('modules.medium_stock.history', compact('data'));
    }

    public function dt(Request $request){
        $data = TcMediumStock::with('tc_mediums:id,name,code')
            ->with('tc_workers:id,code,name')
            ->with('tc_init_bottles')
            ->where('tc_medium_id', $request->id)
            ->where('id', '!=', 99)
        ;
        if($request->id == 0){
            $data = TcMediumStock::query()
            ->with('tc_mediums:id,name,code')
            ->with('tc_workers:id,code,name')
            ->where('id', '!=', 99)
            ;
        }
        // dd($data->get()->toArray());
        return Datatables::of($data)
            ->addColumn('custom_id',function($data){
                return '#'.$data->id;
            })
            ->addColumn('custom_name', function($data){
                $el = '<strong class="mt-0 font-size-14">-</strong>';
                if($data->tc_mediums != null){
                    $el = '<strong class="mt-0 font-size-14">'.$data->tc_mediums->code.'</strong>';
                }
                $url = route('medium-stocks.edit', $data->id);
                $el .= "
                    <p class='mb-0'>
                        <a class='text-primary' href='".$url."'>Edit</a>
                ";

                $el .= "
                        <span class='text-muted'>-</span>
                        <a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                    ";
                $el .= "
                        <span class='text-muted'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#stockValidateModal'>Validate</a>
                    ";
                $el .= "
                        <span class='text-muted'>-</span>
                        <a class='text-primary' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#historyModal'>History</a>
                    ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('composition', function($data){
                $return = '-</br>-';
                if($data->tc_mediums != null){
                    $return = $data->tc_bottles->name.'</br>'.$data->tc_agars->name;
                }
                return $return;
            })
            // ->addColumn('current_stock', function($data){
            //     $stockIn = $data->tc_medium_opname->sum('stock_in');
            //     $stockOut = $data->tc_medium_opname->sum('stock_out');
            //     $stockUsed = $data->tc_init_bottles->count();
            //     $callusTransfer = $data->tc_callus_transfer_stocks->sum('stock_used');
            //     $embryoTransfer = $data->tc_embryo_transfer_stocks->sum('used_stock');
            //     $liquidTransfer = $data->tc_liquid_transfer_stocks->sum('used_stock');
            //     $maturTransfer = $data->tc_matur_transfer_stocks->sum('used_stock');
            //     $stock = $data->stock;
            //     $currentStock = $stock + $stockIn -
            //         $stockOut - $stockUsed - $callusTransfer -
            //         $embryoTransfer - $liquidTransfer - $maturTransfer
            //     ;
            //     return $currentStock;
            // })

            ->rawColumns(['custom_name', 'composition'])
            ->toJson();

    }

    public function index()
    {
        $data['title'] = "Medium Stock Data";
        $data['desc'] = "Display all available Medium Stock";
        $TcMedium = new TcMedium();
        $data['tc_mediums'] = $TcMedium->selData();

        return view('modules.medium_stock.index', compact('data'));
    }
    public function indexFilter($id)
    {
        $data['title'] = "Medium Stock Data";
        $data['desc'] = "Display all available Medium Stock";
        $TcMedium = new TcMedium();
        $data['tc_mediums'] = $TcMedium->selData();
        $data['filter'] = $id;

        return view('modules.medium_stock.index', compact('data'));
    }

    public function create()
    {
        $data['title'] = "Add New Medium Stock";
        $data['desc'] = "Form Add New Medium Stock";

        $TcMedium = new TcMedium();
        $data['tc_mediums'] = $TcMedium->selData();
        $TcBottle = new TcBottle();
        $data['tc_vassels'] = $TcBottle->selData();
        $TcAgar = new TcAgar();
        $data['tc_agars'] = $TcAgar->selData();
        $TcWorker = new TcWorker();
        $data['tc_workers'] = $TcWorker->selData();

        return view('modules.medium_stock.create', compact('data'));
    }

    public function createParam($id){
        $data['title'] = "Add New Medium Stock";
        $data['desc'] = "Form Add New Medium Stock";

        $TcMedium = new TcMedium();
        $data['tc_mediums'] = $TcMedium->selData();
        $TcBottle = new TcBottle();
        $data['tc_vassels'] = $TcBottle->selData();
        $TcAgar = new TcAgar();
        $data['tc_agars'] = $TcAgar->selData();
        $TcWorker = new TcWorker();
        $data['tc_workers'] = $TcWorker->selData();
        $data['selected'] = $id;

        return view('modules.medium_stock.create', compact('data'));
    }

    public function store(MediumStockCreate $request)
    {
        $TcMediumStock = new TcMediumStock();
        $data = $request->except('_token');
        $TcMediumStock->inData($data);
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

    public function edit($id)
    {
        $data['title'] = "Edit Medium Stock";
        $data['desc'] = "Edit Medium Stock";

        $TcMediumStock = new TcMediumStock();
        $data['data_edit'] = $TcMediumStock->selByCol('id', $id)->first();
        $TcMedium = new TcMedium();
        $data['tc_mediums'] = $TcMedium->selData();
        $TcBottle = new TcBottle();
        $data['tc_bottles'] = $TcBottle->selData();
        $TcAgar = new TcAgar();
        $data['tc_agars'] = $TcAgar->selData();
        $TcWorker = new TcWorker();
        $data['tc_workers'] = $TcWorker->selData();
        // dd($data['data_edit']);

        return view('modules.medium_stock.edit', compact('data'));
    }

    public function update(MediumStockEdit $request, $id)
    {

        $data = $request->except('_token', '_method','id');
        $TcMediumStock = new TcMediumStock();
        $TcMediumStock->upData($id, $data);
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
        $TcMediumStock = new TcMediumStock();
        $TcMediumStock->delData($id);
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
