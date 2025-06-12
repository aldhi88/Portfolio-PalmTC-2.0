<?php

namespace App\Http\Controllers;

use App\Models\TcCallusComment;
use App\Models\TcCallusOb;
use App\Models\TcCallusObDetail;
use App\Models\TcContamination;
use App\Models\TcInit;
use App\Models\TcInitBottle;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CallusObController extends Controller
{
    public function index()
    {
        $data['title'] = "Callus Observation Data";
        $data['desc'] = "Display all observation summary by sample data";
        $q = TcInit::get()->count();
        $data['dtInit'] = $q == 0 ? false : true;
        return view('modules.callus_ob.index', compact('data'));
    }
    public function dt(Request $request)
    {
        $subqueryBottleCallus = DB::table('tc_callus_ob_details')
            ->selectRaw('COUNT(DISTINCT tc_init_bottle_id)')
            ->whereIn('tc_callus_ob_id', function ($query) {
                $query->select('id')
                    ->from('tc_callus_obs')
                    ->whereColumn('tc_callus_obs.tc_init_id', 'tc_inits.id')
                    ->where('status', 1);
            })
            ->where('result', 1)
            ->where('is_count_bottle', 1);

        $subqueryExplantCallus = DB::table('tc_callus_ob_details')
            ->selectRaw('COUNT(DISTINCT tc_init_bottle_id)')
            ->whereIn('tc_callus_ob_id', function ($query) {
                $query->select('id')
                    ->from('tc_callus_obs')
                    ->whereColumn('tc_callus_obs.tc_init_id', 'tc_inits.id')
                    ->where('status', 1);
            })
            ->where('result', 1)
            ->where('is_count_explant', 1);

        $subqueryBottleOxi = DB::table('tc_callus_ob_details')
            ->selectRaw('COUNT(DISTINCT tc_init_bottle_id)')
            ->whereIn('tc_callus_ob_id', function ($query) {
                $query->select('id')
                    ->from('tc_callus_obs')
                    ->whereColumn('tc_callus_obs.tc_init_id', 'tc_inits.id')
                    ->where('status', 1);
            })
            ->where('result', 2)
            ->where('is_count_bottle', 1);

        $subqueryBottleContam = DB::table('tc_callus_ob_details')
            ->selectRaw('COUNT(DISTINCT tc_init_bottle_id)')
            ->whereIn('tc_callus_ob_id', function ($query) {
                $query->select('id')
                    ->from('tc_callus_obs')
                    ->whereColumn('tc_callus_obs.tc_init_id', 'tc_inits.id')
                    ->where('status', 1);
            })
            ->where('result', 3)
            ->where('is_count_bottle', 1);

        $subqueryExplantContam = DB::table('tc_callus_ob_details')
            ->selectRaw('COUNT(*)') // <== bukan COUNT DISTINCT
            ->whereIn('tc_callus_ob_id', function ($query) {
                $query->select('id')
                    ->from('tc_callus_obs')
                    ->whereColumn('tc_callus_obs.tc_init_id', 'tc_inits.id')
                    ->where('status', 1);
            })
            ->where('result', 3)
            ->where('is_count_explant', 1);


        $data = TcInit::select('tc_inits.*')
            ->addSelect([
                'bottle_callus' => $subqueryBottleCallus,
                'explant_callus' => $subqueryExplantCallus,
                'bottle_oxi' => $subqueryBottleOxi,
                'bottle_contam' => $subqueryBottleContam,
                'explant_contam' => $subqueryExplantContam,
            ])
            ->with([
                "tc_samples",
                "tc_callus_obs"
            ])
            ->withCount([
                "tc_callus_obs" => function ($q) {
                    $q->where('status', 1);
                },
                // Tambahkan ini
                'tc_init_bottles as total_bottle' => function ($q) {
                    $q->where('status', 1);
                }
            ]);
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->toJson();
    }
    // public function dt(Request $request)
    // {
    //     $data = TcInit::select('tc_inits.*')
    //         ->with([
    //             "tc_samples",
    //             "tc_callus_obs"
    //         ])
    //         ->withCount([
    //             "tc_callus_obs" => function ($q) {
    //                 $q->where('status', 1);
    //             }
    //         ]);
    //     // dd($data->get()->toArray());
    //     return DataTables::of($data)
    //         ->addColumn('reminder', function ($data) {
    //             return '';
    //         })
    //         ->addColumn('sample_action', function ($data) {
    //             $q = TcCallusOb::where('tc_init_id', $data->id)
    //                 ->latest()
    //                 ->first();
    //             $el = '<p class="mb-0"><strong>' . $data->tc_samples->sample_number_display . '</strong></p>';
    //             $el .= "
    //                 <p class='mb-0'>
    //                     <a class='text-primary' href='" . route('callus-obs.show', $data->id) . "'>View</a>
    //             ";

    //             $el .= "
    //                     <span class='text-muted'>-</span>
    //                     <a class='text-link' href='" . route('callus-obs.create', $q->id) . "'>Observation</a>
    //                 ";
    //             $el .= "
    //                     <span class='text-muted'>-</span>
    //                     <a class='text-primary' data-id='" . $data->id . "' href='" . route('callus-obs.comment', $data->id) . "'>Comment</a>
    //                 ";
    //             $el .= '</p>';
    //             return $el;
    //         })
    //         ->addColumn('init_date', function ($data) {
    //             return Carbon::parse($data->created_at)->format('d/m/Y');
    //         })
    //         ->addColumn('bottle_callus', function ($data) {
    //             return TcCallusObDetail::getTotalBottleCallusByInit($data->id);
    //         })
    //         ->addColumn('persen_bottle_callus', function ($data) {
    //             $bottleCallus = TcCallusObDetail::getTotalBottleCallusByInit($data->id);
    //             $totalBottle = TcInitBottle::select('bottle_number')
    //                 ->where('tc_init_id', $data->id)
    //                 ->where('status', 1)
    //                 ->get()
    //                 ->count();
    //             return number_format($bottleCallus / $totalBottle * 100, 2, ',', '.');
    //         })
    //         ->addColumn('explant_callus', function ($data) {
    //             return TcCallusObDetail::getTotalExplantCallusByInit($data->id);
    //         })
    //         ->addColumn('persen_explant_callus', function ($data) {
    //             $bottleCallus = TcCallusObDetail::getTotalExplantCallusByInit($data->id);
    //             $totalBottle = TcInitBottle::select('bottle_number')
    //                 ->where('tc_init_id', $data->id)
    //                 ->where('status', 1)
    //                 ->get()
    //                 ->count();
    //             $totalExplant = $totalBottle * $data->number_of_plant;
    //             return number_format($bottleCallus / $totalExplant * 100, 2, ',', '.');
    //         })
    //         ->addColumn('bottle_oxi', function ($data) {
    //             return TcCallusObDetail::getTotalBottleOxiByInit($data->id);
    //         })
    //         ->addColumn('explant_oxi', function ($data) {
    //             return TcCallusObDetail::getTotalBottleOxiByInit($data->id) * $data->number_of_plant;
    //         })
    //         ->addColumn('bottle_contam', function ($data) {
    //             return TcCallusObDetail::getTotalBottleContamByInit($data->id);
    //         })
    //         ->addColumn('explant_contam', function ($data) {
    //             // return TcCallusObDetail::getTotalExplantContamByInit($data->id) * $data->number_of_plant;
    //             return TcCallusObDetail::getTotalExplantContamByInit($data->id);
    //         })
    //         ->rawColumns(['sample_action'])
    //         ->toJson();
    // }

    public function create($obsId)
    {
        $data['title'] = "Callus Observation Data";
        $data['desc'] = "Display all observation summary by sample data";

        $qObs = TcCallusOb::where('id', $obsId)
            ->with('tc_inits')
            ->orderBy('date_ob', 'desc')
            ->get();
        $data['date_ob'] = $qObs->first()->status == 0 ? date('Y-m-d') : Carbon::parse($qObs->first()->date_ob)->format('Y-m-d');
        $data['worker_now'] = $qObs->first()->status == 0 ? null : $qObs->first()->tc_worker_id;
        $data['init'] = $qObs->first()->tc_inits;
        $data['bottles'] = TcInitBottle::where('tc_init_id', $data['init']->id)->get();
        $data['lastObs'] = (count($qObs) - 1) == 0 ? '--/--/----' : Carbon::parse($qObs->first()->date_ob)->format('d M Y');
        $data['countObs'] = count($qObs);
        $data['start'] = ($qObs->first()->status == 0) ? false : true;
        $data['initId'] = $qObs->first()->tc_init_id;
        $data['obsId'] = $qObs->first()->id;
        $data['schedule'] = Carbon::parse($qObs->first()->date_schedule)->format('d/m/Y');

        $data['workers'] = TcWorker::where('status', 1)->where('id', '!=', 0)->get();

        return view('modules.callus_ob.create_by_sample', compact('data'));
    }
    public function startObs(Request $request)
    {
        $q = TcCallusOb::where('id', $request->id)->get();
        $initId = $q->first()->tc_init_id;
        $dateObLbhKecil = TcCallusOb::where('tc_init_id', $initId)
            ->where('date_ob', '>=', $request->date_ob)
            ->where('id', '<', $request->id)
            ->get()
            ->count();
        if ($dateObLbhKecil != 0) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Failed, observation date not valid, try another date.',
                ],
            ]);
        }

        if ($request->action == 'start') {
            $q = TcCallusOb::where('id', $request->id)
                ->update([
                    'status' => 1,
                    'date_ob' => $request->date_ob,
                    'tc_worker_id' => $request->tc_worker_id
                ]);

            $q = TcCallusOb::where('id', $request->id)
                ->orderBy('date_ob', 'desc')
                ->first();

            $dtObs['date_schedule'] = Carbon::parse($q->date_ob)->addMonths(1);
            $dtObs['date_ob'] = $dtObs['date_schedule'];
            $dtObs['tc_init_id'] = $q->tc_init_id;
            $dtObs['status'] = 0;
            TcCallusOb::create($dtObs);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => 'success',
                    'icon' => 'check',
                    'el' => 'alert-area',
                    'msg' => 'Success, observation data has been created.',
                    'status' => 'update',
                ],
            ]);
        } else {
            TcCallusOb::where('id', $request->id)
                ->update([
                    'date_ob' => $request->date_ob,
                    'tc_worker_id' => $request->tc_worker_id
                ]);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => 'success',
                    'icon' => 'check',
                    'el' => 'alert-area',
                    'msg' => 'Success, date of observation has been updated.',
                ],
            ]);
        }
    }
    public function dtBottle(Request $request)
    {
        $initId = $request->initId;
        $obsId = $request->obsId;
        $explantNumber = TcInit::where('id', $initId)
            ->first()
            ->getAttribute('number_of_plant');
        $sub = DB::table('tc_callus_ob_details as a')
            ->selectRaw("
                a.tc_init_bottle_id,
                STUFF((
                    SELECT ', ' + CAST(b.result AS VARCHAR)
                    FROM tc_callus_ob_details b
                    WHERE b.tc_init_bottle_id = a.tc_init_bottle_id
                    ORDER BY b.id
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS result_list
            ")
            ->groupBy('a.tc_init_bottle_id');


        $data = TcInitBottle::select([
            'tc_init_bottles.*',
            'tc_workers.code as worker_code',
            'result_sub.result_list',
        ])
            ->where('tc_init_bottles.tc_init_id', $initId)
            ->leftJoin('tc_workers', 'tc_workers.id', '=', 'tc_init_bottles.tc_worker_id')
            ->leftJoinSub($sub, 'result_sub', function ($join) {
                $join->on('tc_init_bottles.id', '=', 'result_sub.tc_init_bottle_id');
            });

        return DataTables::of($data)
            ->addColumn('explant_number', function ($data) use ($explantNumber, $obsId) {
                $el = null;
                $qOption = TcContamination::all();
                $checkDisable = TcCallusObDetail::where('tc_callus_ob_id', '<', $obsId)
                    ->where('tc_init_bottle_id', $data->id)
                    ->where('result', '!=', 1)
                    ->get();
                if (count($checkDisable) > 0) {
                    $result = $checkDisable->first()->result;
                    $text = $result == 2 ? "OXIDATION" : "CONTAMINATION (" . $checkDisable->first()->tc_contaminations->code . ")";
                    $el .= '
                        <label class="badge badge-light-danger mb-0 d-block w-100 rounded-0">' . $text . '</label>
                    ';
                } else {
                    for ($i = 1; $i <= $explantNumber; $i++) {
                        // checked or not
                        $countCallus = TcCallusObDetail::where('tc_callus_ob_id', $obsId)
                            ->where('tc_init_bottle_id', $data->id)
                            ->where('explant_number', $i)
                            ->where('result', 1)
                            ->get()
                            ->count();
                        $callusCheck = $countCallus > 0 ? 'checked' : null;
                        $countOxi = TcCallusObDetail::where('tc_callus_ob_id', $obsId)
                            ->where('tc_init_bottle_id', $data->id)
                            ->where('explant_number', $i)
                            ->where('result', 2)
                            ->get()
                            ->count();
                        $oxiCheck = $countOxi > 0 ? 'checked' : null;

                        $dtOption = null;
                        foreach ($qOption as $key => $value) {
                            $countContam = TcCallusObDetail::where('tc_callus_ob_id', $obsId)
                                ->where('tc_init_bottle_id', $data->id)
                                ->where('explant_number', $i)
                                ->where('result', 3)
                                ->where('tc_contamination_id', $value->id)
                                ->get()
                                ->count();
                            $contamCheck = $countContam > 0 ? 'selected' : null;
                            $dtOption .= '<option ' . $contamCheck . ' value="' . $value->id . '">' . $value->code . '</option>';
                        }
                        $border = $i != $explantNumber ? 'border-bottom' : null;

                        $callusCount = TcCallusObDetail::where('tc_callus_ob_id', '<', $obsId)
                            ->where('tc_init_bottle_id', $data->id)
                            ->where('explant_number', $i)
                            ->where('result', 1)
                            ->get()
                            ->count();

                        $el .= '
                            <div class="row ' . $border . '">
                                <div class="col-3 mt-1">
                                    <div class="row">
                                        <div class="col-5">
                                            <label class="badge badge-light-success border border-success d-block w-100 rounded-0 px-0">' . $callusCount . '</label>
                                        </div>
                                        <div class="col px-0"><i class="fas fa-leaf text-success"></i> ' . $i . '.</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group p-0 mb-0 form-check mt-1">
                                        <input ' . $callusCheck . ' type="checkbox" name="result" value="1" data-bottle="' . $data->id . '" data-explant="' . $i . '" class="form-check-input check-callus" id="callus_' . $data->bottle_number . $i . '">
                                        <label class="form-check-label" for="callus_' . $data->bottle_number . $i . '">Callus</label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group p-0 mb-0 form-check mt-1">
                                        <input ' . $oxiCheck . ' type="checkbox" name="result" value="2" data-bottle="' . $data->id . '" data-explant="' . $i . '" class="form-check-input" id="oxi_' . $data->bottle_number . $i . '">
                                        <label class="form-check-label" for="oxi_' . $data->bottle_number . $i . '">Oxidate</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <select class="form-control form-control-sm" data-bottle="' . $data->id . '">
                                        <option value="0">Uncontaminated</option>
                                        ' . $dtOption . '
                                    </select>
                                </div>
                            </div>
                        ';
                    }
                }
                return $el;
            })
            ->addColumn('form_search', fn($row) => $row->result_list ?: '-')
            ->filterColumn('form_search', function ($query, $keyword) {
                $query->whereRaw("EXISTS (
                    SELECT 1 FROM tc_callus_ob_details
                    WHERE tc_callus_ob_details.tc_init_bottle_id = tc_init_bottles.id
                    AND tc_callus_ob_details.result LIKE ?
                )", ["%{$keyword}%"]);
            })

            ->rawColumns(['explant_number'])
            ->smart(true)
            ->toJson();
    }
    public function store(Request $request)
    {
        $data = $request->except('action');
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        if ($data['result'] == 1) {
            // Cek apakah bottle_callus perlu ditingkatkan
            $qBottleCallus = TcCallusObDetail::where('tc_callus_ob_id', $request->tc_callus_ob_id)
                ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                ->where('result', 1)
                ->count();

            if ($qBottleCallus == 0) {
                TcCallusOb::where('id', $request->tc_callus_ob_id)->increment('bottle_callus');
            }

            // Set is_count_bottle
            $data['is_count_bottle'] = TcCallusObDetail::where('result', 1)
                ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                ->where('tc_callus_ob_id', '<', $request->tc_callus_ob_id)
                ->exists() ? 0 : 1;

            // Set is_count_explant
            $data['is_count_explant'] = TcCallusObDetail::where('result', 1)
                ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                ->where('explant_number', $request->explant_number)
                ->where('tc_callus_ob_id', '<', $request->tc_callus_ob_id)
                ->exists() ? 0 : 1;

            if ($request->action == 'insert') {
                TcCallusObDetail::where('tc_callus_ob_id', $data['tc_callus_ob_id'])
                    ->where('tc_init_bottle_id', $data['tc_init_bottle_id'])
                    ->where('result', '!=', 1)
                    ->forceDelete();

                TcCallusObDetail::create($data);
            } else {
                TcCallusObDetail::where('tc_callus_ob_id', $data['tc_callus_ob_id'])
                    ->where('tc_init_bottle_id', $data['tc_init_bottle_id'])
                    ->where('explant_number', $data['explant_number'])
                    ->where('result', 1)
                    ->forceDelete();

                if (!TcCallusObDetail::where('tc_callus_ob_id', $request->tc_callus_ob_id)
                    ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                    ->where('result', 1)
                    ->exists()) {
                    TcCallusOb::where('id', $request->tc_callus_ob_id)->decrement('bottle_callus');
                }
            }
        } else {
            // Cek apakah bottle_callus perlu dikurangi
            if (TcCallusObDetail::where('tc_callus_ob_id', $request->tc_callus_ob_id)
                ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                ->where('result', 1)
                ->exists()
            ) {
                TcCallusOb::where('id', $request->tc_callus_ob_id)->decrement('bottle_callus');
            }

            $q = TcInitBottle::select('id', 'tc_init_id')
                ->where('id', $data['tc_init_bottle_id'])
                ->first();
            $explantNumber = $q->tc_inits->number_of_plant;

            // Set is_count_bottle
            $data['is_count_bottle'] = TcCallusObDetail::where('result', 1)
                ->where('tc_callus_ob_id', '<', $request->tc_callus_ob_id)
                ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                ->exists() ? 0 : 1;

            // Ambil daftar explant_number yang sudah ada
            $existingExplantNumbers = TcCallusObDetail::where('tc_init_id', $request->tc_init_id)
                ->where('tc_callus_ob_id', '<', $request->tc_callus_ob_id)
                ->where('tc_init_bottle_id', $request->tc_init_bottle_id)
                ->where('result', 1)
                ->pluck('explant_number')
                ->toArray();

            if ($request->action == 'insert') {
                TcCallusObDetail::where('tc_callus_ob_id', $data['tc_callus_ob_id'])
                    ->where('tc_init_bottle_id', $data['tc_init_bottle_id'])
                    ->forceDelete();

                $dt = [];
                for ($i = 1; $i <= $explantNumber; $i++) {
                    $data['explant_number'] = $i;
                    $data['is_count_explant'] = in_array($i, $existingExplantNumbers) ? 0 : 1;
                    $dt[] = $data;
                }
                TcCallusObDetail::insert($dt);
            } else {
                TcCallusObDetail::where('tc_callus_ob_id', $data['tc_callus_ob_id'])
                    ->where('tc_init_bottle_id', $data['tc_init_bottle_id'])
                    ->where('result', $data['result'] == 2 ? 2 : 3)
                    ->forceDelete();
            }
        }
    }

    public function show($id)
    {
        $data['title'] = "Detail Observation";
        $data['desc'] = "Show all observation data of sample";

        $data['initId'] = $id;

        $qInit = TcInit::select('*')
            ->with([
                'tc_samples' => function ($q) {
                    $q->select('id', 'sample_number');
                },
                'tc_rooms'
            ])
            ->where('id', $id)
            ->first();
        $data['sampleNumber'] = $qInit->tc_samples->sample_number_display;
        // ======================
        $qCallusOb = collect(TcCallusOb::where('tc_init_id', $id)->get()->toArray());

        $cekUdahObBlum = $qCallusOb->sortByDesc('date_ob')
            ->where('status', 1)
            ->count();
        if ($cekUdahObBlum) {
            $data['lastDateOb'] = Carbon::parse(
                $qCallusOb->sortByDesc('date_ob')
                    ->where('status', 1)
                    ->first()['date_ob']
            )->format('d M Y');
        } else {
            $data['lastDateOb'] = '-';
        }

        $data['nextDateOb'] = Carbon::parse(
            $qCallusOb->sortByDesc('date_ob')
                ->where('status', 0)
                ->first()['date_ob']
        )->format('d M Y');
        $data['countObDone'] = $qCallusOb->where('status', 1)->count();

        // ==================
        $qInitBottle = collect(
            TcInitBottle::where('tc_init_id', $id)
                ->where('status', 1)
                ->get()
                ->toArray()
        );
        $data['totalBlock'] = $qInitBottle->groupBy('block_number')->count();
        $data['totalBottle'] = $qInitBottle->groupBy('bottle_number')->count();
        $data['totalExplant'] = $data['totalBottle'] * $qInit->number_of_plant;
        $data['totalWorker'] = $qInitBottle->groupBy('tc_worker_id')->count();
        $data['initRoom'] = $qInit->tc_rooms->code;

        // ===============
        $data['totalBottleCallusPerInit'] = TcCallusObDetail::getTotalBottleCallusByInit($id);
        $data['persenBottleCallus'] = number_format($data['totalBottleCallusPerInit'] / $data['totalBottle'] * 100, 2, ',', '.');
        $data['listBottleCallusPerInit'] = TcCallusObDetail::getListBottleCallusByInit($id);
        // ===============
        $data['totalExplantCallusPerInit'] = TcCallusObDetail::getTotalExplantCallusByInit($id);
        $data['persenExplantCallus'] = number_format($data['totalExplantCallusPerInit'] / $data['totalExplant'] * 100, 2, ',', '.');
        $data['listExplantCallusPerInit'] = TcCallusObDetail::getListExplantCallusByInit($id);
        // ===============
        $data['totalBottleOxiPerInit'] = TcCallusObDetail::getTotalBottleOxiByInit($id);
        $data['listBottleOxiPerInit'] = TcCallusObDetail::getListBottleOxiByInit($id);
        $data['totalExplantOxiPerInit'] = TcCallusObDetail::getTotalExplantOxiByInit($id);
        $data['listExplantOxiPerInit'] = TcCallusObDetail::getListExplantOxiByInit($id);

        $data['totalBottleContamPerInit'] = TcCallusObDetail::getTotalBottleContamByInit($id);
        $data['listBottleContamPerInit'] = TcCallusObDetail::getListBottleContamByInit($id);
        $data['totalExplantContamPerInit'] = TcCallusObDetail::getTotalExplantContamByInit($id);
        $data['listExplantContamPerInit'] = TcCallusObDetail::getListExplantContamByInit($id);

        $data['nextObId'] = $qCallusOb->sortBy('status')->first()['id'];


        return view("modules.callus_ob.detail_observation", compact('data'));
    }
    public function dtDetailObs(Request $request)
    {
        $data = TcCallusOb::select([
            "tc_callus_obs.*"
        ])
            ->where('tc_init_id', $request->initId)
            ->where('status', 1)
            ->with('tc_callus_ob_details')
            ->with('tc_workers');

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $el = "
                    <p class='mb-0 font-size-14'>
                        <a class='text-primary' href='" . route('callus-obs.create', $data->id) . "'>Edit</a>
                ";
                $el .= '</p>';
                return $el;
            })
            ->addColumn('date_ob_format', function ($data) use ($request) {
                $el = '<strong class="mt-0 font-size-14">' . Carbon::parse($data->date_ob)->format('d/m/Y') . '</strong>';
                return $el;
            })
            ->addColumn('new_bottle_callus', function ($data) {
                return TcCallusObDetail::getTotalNewBottleCallusByOb($data->id);
            })
            ->addColumn('old_bottle_callus', function ($data) {
                return TcCallusObDetail::getTotalOldBottleCallusByOb($data->id);
            })
            ->addColumn('new_explant_callus', function ($data) {
                return TcCallusObDetail::getTotalNewExplantCallusByOb($data->id);
            })
            ->addColumn('old_explant_callus', function ($data) {
                return TcCallusObDetail::getTotalOldExplantCallusByOb($data->id);
            })
            ->addColumn('bottle_oxi', function ($data) {
                return TcCallusObDetail::getTotalBottleOxiByOb($data->id);
            })
            ->addColumn('explant_oxi', function ($data) {
                return TcCallusObDetail::getTotalExplantOxiByOb($data->id);
            })
            ->addColumn('bottle_contam', function ($data) {
                return TcCallusObDetail::getTotalBottleContamByOb($data->id);
            })
            ->addColumn('explant_contam', function ($data) {
                return TcCallusObDetail::getTotalExplantContamByOb($data->id);
            })
            ->rawColumns(['date_ob_format', 'action'])
            ->toJson();
    }

    public function printObsForm(Request $request)
    {
        $data['title'] = "Print Observation Form";
        $data['desc'] = "Printing observation form before input observation result";
        $id = $request->initId;
        if ($id == 'blank') {
            if ($request->page == 1) {
                $data['totalRow'] = $request->page * 25;
            } else {
                $data['totalRow'] = ($request->page * 27) - 2;
            }
            return view('modules.callus_ob.print.form_obs_blank', compact('data'));
        }

        // $data['init'] = TcInit::where('id',$id)->get()->first();
        // $data['explantNumber'] = $data['init']->number_of_plant;
        // $data['sample_number'] = $data['init']->tc_samples->sample_number_display;
        // $q = TcCallusOb::where('tc_init_id',$id)
        //     ->orderBy('date_ob','desc')
        //     ->get();
        // $data['number_of'] = count($q);
        // $q = TcCallusOb::where('tc_init_id', $id)
        //     ->where('status', 0)
        //     ->orderBy('date_ob','desc')
        //     ->get();
        // $data['schedule'] = Carbon::parse($q->first()->date_schedule)->format('d M Y');

        // if($request->page == 1){
        //     $data['totalRow'] = $request->page * 25;
        // }else{
        //     $data['totalRow'] = ($request->page * 27) - 2;
        // }
        return view('modules.callus_ob.print.form_obs', compact('data'));
    }

    public function comment($id)
    {
        $data['title'] = "Callus Comments - Files - Images";
        $data['desc'] = "Manage data comment, file and image";
        $data['initId'] = $id;
        return view('modules.callus_ob.comment', compact('data'));
    }

    public function dtComment(Request $request)
    {
        $data = TcCallusComment::select([
            'tc_callus_comments.*',
            DB::raw('convert(varchar,created_at, 103) as created_at_format'), //note*
        ])
            ->where('tc_init_id', $request->id)
            // ->with(['tck_acclims:id'])
        ;
        // if($request->filter==1){
        //     $data->whereNull('file')->whereNull('image');
        // }else if($request->filter==2){
        //     $data->whereNull('image');
        // }else if($request->filter==3){
        //     $data->whereNull('file');
        // }
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                // $el = '
                //     <a class="text-primary fs-13" data-id="'.$data->id.'" href="#" data-toggle="modal" data-target="#editCommentModal">Edit</a>
                // ';
                $dtJson['comment'] = $data->comment;
                $dtJson['id'] = $data->id;
                $json = json_encode($dtJson);
                $el = '
                    <a class="text-danger fs-13" data-json=\'' . htmlspecialchars(json_encode($json), ENT_QUOTES, 'UTF-8') . '\' href="#" data-toggle="modal" data-target="#deleteCommentModal">Delete</a>
                ';
                return $el;
            })
            ->filterColumn('created_at_format', function ($query, $keyword) {
                $sql = 'convert(varchar,created_at, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('image_file', function ($data) {
                $el = null;
                if (!is_null($data->file)) {
                    $el = '
                        <a href="' . asset("storage/media/callus/file") . '/' . $data->file . '">
                            <h5><i class="feather mr-2 icon-file"></i>Download</h5>
                        </a>
                    ';
                }

                return $el;
            })
            ->addColumn('image_format', function ($data) {
                $el = null;
                if (!is_null($data->image)) {
                    $el = '
                        <a href="' . asset("storage/media/callus/image") . '/' . $data->image . '" target="_blank">
                        <img src="' . asset("storage/media/callus/image") . '/' . $data->image . '" class="img-thumbnail" width="70">
                        </a>
                    ';
                }
                return $el;
            })
            ->rawColumns(['image_format', 'image_file', 'action'])
            ->smart(false)->toJson();
    }


    public function commentStore(Request $request)
    {
        $dt = $request->except('_token', 'file', 'image');
        if ($request->hasFile('file')) {
            $dt['file'] = Str::uuid() . '.' . ($request->file('file'))->getClientOriginalExtension();
            ($request->file('file'))->storeAs('public/media/callus/file', $dt['file']);
        }
        if ($request->hasFile('image')) {
            $dt['image'] = Str::uuid() . '.' . ($request->file('image'))->getClientOriginalExtension();
            ($request->file('image'))->storeAs('public/media/callus/image', $dt['image']);
        }

        TcCallusComment::create($dt);

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

    public function commentDestroy(Request $request)
    {
        $data = TcCallusComment::find($request->id);
        Storage::delete('public/media/callus/file/' . $data->file);
        Storage::delete('public/media/callus/image/' . $data->image);
        TcCallusComment::find($request->id)->delete();
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data has been deleted.',
            ],
        ]);
    }
}
