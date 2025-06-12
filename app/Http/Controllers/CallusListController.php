<?php

namespace App\Http\Controllers;

use App\Exports\CallusListExport;
use App\Models\TcCallusObDetail;
use App\Models\TcCallusTransfer;
use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoOb;
use App\Models\TcInit;
use App\Models\TcSample;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class CallusListController extends Controller
{
    public function index()
    {
        $dtSamples = TcSample::select(
                "created_at",
                DB::raw('YEAR(created_at) year')
            )
            ->orderBy("created_at","ASC")
            ->whereHas('tc_inits')
            ->get();
        $data["years"] = [];
        foreach ($dtSamples as $key => $value) {
            if(!in_array($value->year,$data["years"])){
                array_push($data["years"],$value->year);
            }
        }
        $data['title'] = "Callogenesis Report List";
        $data['desc'] = "Display report summary all sample data";
        return view('modules.callus_list.index',compact('data'));
    }
    public function dt(Request $request)
    {
        $data = TcInit::select([
                'tc_inits.*',
                DB::raw('
                    number_of_plant * (
                        SELECT COUNT(*) FROM tc_init_bottles WHERE status=1 AND tc_init_id=tc_inits.id
                    ) AS total_explant
                ') //note!
            ])
            ->withCount([
                'tc_init_bottles as total_bottle' =>function($q){
                    $q->where('status',1);
                }
            ])
            ->with([
                'tc_samples',
                'tc_samples.master_treefile'
            ])
        ;
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->addColumn('created_at_format',function($data){
                return Carbon::parse($data->tc_samples->created_at)->format('d/m/Y');
            })
            ->addColumn('total_explant_callus',function($data){
                return TcCallusObDetail::getTotalExplantCallusByInit($data->id);
            })
            ->addColumn('persen_explant_callus',function($data){
                $totalEx = $data->total_bottle * $data->number_of_plant;
                $exCallus = TcCallusObDetail::getTotalExplantCallusByInit($data->id);
                return number_format($exCallus/$totalEx*100,2,',','.');
            })
            ->addColumn('total_bottle_callus',function($data){
                $totalAwal = TcEmbryoBottle::where('tc_init_id',$data->id)->sum('number_of_bottle');
                $totalUsed = TcEmbryoBottle::usedBottleByInit($data->id);
                return $totalAwal - $totalUsed;
            })
            ->addColumn('end_date',function($data){
                return is_null($data->date_stop)?'On Going':Carbon::parse($data->date_stop)->format('d/m/Y');
            })
            ->smart(false)
            ->toJson();
    }
    public function exportPrint(Request $request){
        $from = $request->from.'-01-01 00:00:00';
        $to = $request->to.'-12-31 23:59:59';

        $q = TcInit::select([
                'id','tc_sample_id','number_of_plant',
                DB::raw('
                    number_of_plant * (
                        SELECT COUNT(*) FROM tc_init_bottles WHERE status=1 AND tc_init_id=tc_inits.id
                    ) AS total_explant
                ') //note!
            ])
            ->with([
                'tc_samples:id,master_treefile_id,program,created_at,program,sample_number',
                'tc_samples.master_treefile:id,tipe'
            ])
            ->whereHas('tc_samples',function(Builder $query) use($from,$to){
                $query->select('id','master_treefile_id','created_at','sample_number','program')
                    ->whereBetween('created_at',[$from,$to]);
            })
            ->get();
        foreach ($q as $key => $value) {
            $exCallus = TcCallusObDetail::getTotalExplantCallusByInit($value->id);
            $totalAwal = TcEmbryoBottle::where('tc_init_id',$value->id)->sum('number_of_bottle');
            $totalUsed = TcEmbryoBottle::usedBottleByInit($value->id);
            $data[] = [
                'sampling' => $value->tc_samples->sample_number_display,
                'sampling_date' => Carbon::parse($value->tc_samples->created_at)->format('d/m/Y'),
                'total_explant' => $value->total_explant,
                'total_explant_callus' => $exCallus,
                'persen_explant_callus' => number_format($exCallus/$value->total_explant*100,2,',','.'),
                'total_bottle_callus' => $totalAwal - $totalUsed,
                'type' => $value->tc_samples->master_treefile->tipe,
                'program' => $value->tc_samples->program,
                'remarks' => is_null($value->date_stop)?'On Going':Carbon::parse($value->date_stop)->format('d/m/Y'),
            ];
        }

        $data['samples'] = $data;

        $data['title'] = "Print Report";
        $data['desc'] = "Print selected report.";
        return view('modules.callus_list.print.download_print', compact('data'));
    }
    public function exportExcel(Request $request){
        $dateTime = Carbon::now()->getPreciseTimestamp(3);
        $fileName = now()->format('Y_m_d_').substr(md5($dateTime),0,8).".xlsx";
        $save = (new CallusListExport($request->all()))->store($fileName, 'dir_excel_temp');
        return response()->json([
            'data' => [
                'link' => asset('storage/excel_temp'),
                'filename' => $fileName,
            ],
        ]);
    }
    public function exportPDF(Request $request){
        $from = $request->from.'-01-01 00:00:00';
        $to = $request->to.'-12-31 23:59:59';
        $q = TcInit::select([
                'id','tc_sample_id','number_of_plant',
                DB::raw('
                    number_of_plant * (
                        SELECT COUNT(*) FROM tc_init_bottles WHERE status=1 AND tc_init_id=tc_inits.id
                    ) AS total_explant
                ') //note!
            ])
            ->with([
                'tc_samples:id,master_treefile_id,program,created_at,program,sample_number',
                'tc_samples.master_treefile:id,tipe'
            ])
            ->whereHas('tc_samples',function(Builder $query) use($from,$to){
                $query->select('id','master_treefile_id','created_at','sample_number','program')
                    ->whereBetween('created_at',[$from,$to]);
            })
            ->get();
        foreach ($q as $key => $value) {
            $exCallus = TcCallusObDetail::getTotalExplantCallusByInit($value->id);
            $totalAwal = TcEmbryoBottle::where('tc_init_id',$value->id)->sum('number_of_bottle');
            $totalUsed = TcEmbryoBottle::usedBottleByInit($value->id);
            $data[] = [
                'sampling' => $value->tc_samples->sample_number_display,
                'sampling_date' => Carbon::parse($value->tc_samples->created_at)->format('d/m/Y'),
                'total_explant' => $value->total_explant,
                'total_explant_callus' => $exCallus,
                'persen_explant_callus' => number_format($exCallus/$value->total_explant*100,2,',','.'),
                'total_bottle_callus' => $totalAwal - $totalUsed,
                'type' => $value->tc_samples->master_treefile->tipe,
                'program' => $value->tc_samples->program,
                'remarks' => is_null($value->date_stop)?'On Going':Carbon::parse($value->date_stop)->format('d/m/Y'),
            ];
        }
        $data['samples'] = collect($data);
        $data['title'] = "Export to PDF";
        $data['desc'] = "Display layout before convert to PDF file.";
        $pdf = PDF::loadView('modules.callus_list.print.download_pdf', $data)->setPaper('a4', 'landscape');

        $dateTime = Carbon::now()->getPreciseTimestamp(3);
        $fileName = now()->format('Y_m_d_').substr(md5($dateTime),0,8).".pdf";
        // return $pdf->download($fileName);
        Storage::makeDirectory("public/pdf_temp");
        $path = public_path("storage/pdf_temp/");
        $pdf->save($path . $fileName);
        return response()->json([
            'data' => [
                'link' => asset('storage/pdf_temp'),
                'filename' => $fileName,
            ],
        ]);
    }
}
