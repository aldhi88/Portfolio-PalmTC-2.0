<?php

namespace App\Http\Controllers;

use App\Models\TcMigration;
use Illuminate\Http\Request;
use DataTables;

class MigrationController extends Controller
{
    public function index(){
        $data['title'] = "Migration Data";
        $data['desc'] = "Display all available migration data";
        return view('modules.migration.index', compact('data'));
    }

    public function dtIndex(Request $request)
    {
        $data = TcMigration::query();
        return DataTables::of($data)
            ->addColumn('batch_format',function($data){
                $el = '<input type="number" data-id="'.$data->id.'" class="batch" value="'.$data->batch.'" name="batch_'.$data->id.'">';
                return $el;
            })
            ->rawColumns(['batch_format'])
            ->addIndexColumn()
            ->toJson();
    }

    public function update(Request $request)
    {
        TcMigration::where('id',$request->id)->update(['batch' => $request->value]);
    }
}
