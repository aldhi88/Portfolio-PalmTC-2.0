<?php

namespace App\Console\Commands;

use App\Models\TcAgar;
use App\Models\TcBottle;
use App\Models\TcBottleInit;
use App\Models\TcContamination;
use App\Models\TcDeath;
use App\Models\TcLaminar;
use App\Models\TcMedium;
use App\Models\TcMediumStock;
use App\Models\TcPlantation;
use App\Models\TcRoom;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the required data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TcBottleInit::query()->forceDelete();
        $data = [
            1 => ["keyword" => "liquid_column1", "column_name" => 'column 1', "order" => 1],
            2 => ["keyword" => "liquid_column2", "column_name" => 'column 2', "order" => 2],
            3 => ["keyword" => "matur_column1", "column_name" => 'column 1', "order" => 3],
            4 => ["keyword" => "matur_column2", "column_name" => 'column 2', "order" => 4],
            5 => ["keyword" => "germin_column1", "column_name" => 'column 1', "order" => 5],
            6 => ["keyword" => "germin_column2", "column_name" => 'column 2', "order" => 6],
            7 => ["keyword" => "rooting_column1", "column_name" => 'column 1', "order" => 7],
            8 => ["keyword" => "rooting_column2", "column_name" => 'column 2', "order" => 8],
        ];

        foreach ($data as $key => $value) {
            $keyword = $value['keyword'];
            $q = TcBottleInit::where('keyword', $keyword)->get()->count();
            if ($q == 0) {
                TcBottleInit::create($value);
            }
        }

        // tc_workers
        unset($data);
        $q = TcWorker::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['no_pekerja'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        $data['date_of_birth'] = Carbon::now();
        $data['status'] = 1;
        DB::unprepared('SET IDENTITY_INSERT tc_workers ON');
        TcWorker::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_workers OFF');

        // tc_bottles
        unset($data);
        $q = TcBottle::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        DB::unprepared('SET IDENTITY_INSERT tc_bottles ON');
        TcBottle::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_bottles OFF');

        // tc_agars
        unset($data);
        $q = TcAgar::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        DB::unprepared('SET IDENTITY_INSERT tc_agars ON');
        TcAgar::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_agars OFF');

        // tc_mediums
        unset($data);
        $q = TcMedium::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        DB::unprepared('SET IDENTITY_INSERT tc_mediums ON');
        TcMedium::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_mediums OFF');

        // tc_medium_stocks
        unset($data);
        $q = TcMediumStock::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['tc_bottle_id'] = 99;
        $data['tc_agar_id'] = 99;
        $data['tc_medium_id'] = 99;
        $data['tc_worker_id'] = 99;
        $data['stock'] = 0;
        DB::unprepared('SET IDENTITY_INSERT tc_medium_stocks ON');
        TcMediumStock::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_medium_stocks OFF');

        // tc_laminars
        unset($data);
        $q = TcLaminar::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        DB::unprepared('SET IDENTITY_INSERT tc_laminars ON');
        TcLaminar::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_laminars OFF');

        // tc_plantations
        unset($data);
        TcPlantation::query()->forceDelete();
        $data = [
            [
                'id' => 1,
                'code' => 'AL',
                'name' => 'Aek Loba',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'code' => 'BB',
                'name' => 'Bangun Bandar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'code' => 'SSPL',
                'name' => 'SSPL',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        DB::unprepared('SET IDENTITY_INSERT tc_plantations ON');
        TcPlantation::insert($data);
        DB::unprepared('SET IDENTITY_INSERT tc_plantations OFF');

        // tc_rooms
        unset($data);
        $q = TcRoom::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        DB::unprepared('SET IDENTITY_INSERT tc_rooms ON');
        TcRoom::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_rooms OFF');

        // tc_contaminations
        unset($data);
        $q = TcContamination::find(99);
        if($q){
            $q->forceDelete();
        }
        $data['id'] = 99;
        $data['code'] = "XX";
        $data['name'] = "IMPORT";
        DB::unprepared('SET IDENTITY_INSERT tc_contaminations ON');
        TcContamination::create($data);
        DB::unprepared('SET IDENTITY_INSERT tc_contaminations OFF');

        // tc_deaths
        unset($data);
        TcDeath::query()->forceDelete();
        $data = [
            [
                'id' => 1,
                'code' => 'Mati',
                'name' => 'Mati',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'code' => 'Kering',
                'name' => 'Kering',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        DB::unprepared('SET IDENTITY_INSERT tc_deaths ON');
        TcDeath::insert($data);
        DB::unprepared('SET IDENTITY_INSERT tc_deaths OFF');


        echo "Success, data require has been generated.\n";
    }
}
