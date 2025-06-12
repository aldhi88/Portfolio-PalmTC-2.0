<?php

namespace App\Imports;

use App\Models\TcCallusOb;
use App\Models\TcInit;
use App\Models\TcInitBottle;
use App\Models\TcSample;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;


class InitsImport implements ToCollection
{
    public static $error;

    public function collection(Collection $rows)
    {
        self::$error = false;

        // Ambil ID terakhir dari TcInit
        $lastId = TcInit::max('id') ?? 0;
        $currentId = $lastId;

        $tcInitData = [];
        $tcCallusObsData = [];
        $tcInitBottleData = [];
        $dtSample = TcSample::query()
            ->select('id', 'sample_number')
            ->get();

        // Step 1: Prepare TcInit and TcCallusOb data
        foreach ($rows as $key => $value) {
            if ($key === 0) { continue; }
            if ($key === 0 || (isset($value[0]) && $value[0] === '<end>')) {
                break;
            }

            if(
                empty($value[0])||empty($value[1]) ||
                $value[0]=='-'||$value[1]=='-'
            ){
                self::$error = "Pada data excel ada data value yang kosong / tidak valid. Cek baris ke- " . ($key + 1);
                return;
            }

            $currentId++;

            $cek = $dtSample->firstWhere('sample_number', $value[0]);
            if($cek){
                $sampleId = $cek['id'];
            }else{
                self::$error = "Sample Number ".$value[0]." tidak ditemukan. Cek baris ke- " . ($key + 1);
                return;
            }

            $tcInitData[] = [
                'id' => $currentId,
                'tc_sample_id' => $value[0],
                'tc_room_id' => 99,
                'number_of_block' => 60,
                'number_of_bottle' => 8,
                'number_of_plant' => 3,
                'desc' => "IMPORT DATA",
                'date_work' => Carbon::createFromFormat('d/m/Y', $value[1])->format('Y-m-d'),
                'date_stop' => null,
                'created_at' => Carbon::createFromFormat('d/m/Y', $value[1])->format('Y-m-d'),
                'updated_at' => Carbon::now(),
            ];

            $tcCallusObsData[] = [
                'tc_init_id' => $currentId,
                'date_schedule' => Carbon::createFromFormat('d/m/Y', $value[1])->addMonths(3)->format('Y-m-d'),
                'date_ob' => Carbon::createFromFormat('d/m/Y', $value[1])->addMonths(3)->format('Y-m-d'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $startBottle = 1;
            for ($blockNumber = 1; $blockNumber <= 60; $blockNumber++) {
                for ($bottleNumber = $startBottle; $bottleNumber < ($startBottle + 8); $bottleNumber++) {
                    $tcInitBottleData[] = [
                        'tc_init_id' => $currentId,
                        'block_number' => $blockNumber,
                        'bottle_number' => $bottleNumber,
                        'tc_worker_id' => 99,
                        'tc_laminar_id' => 99,
                        'tc_medium_stock_id' => 99,
                        'status' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                $startBottle = $bottleNumber;
            }
        }

        if ($this->cekNotExistData($tcInitData)) {
            return;
        }

        if (!empty($tcInitData)) {
            DB::unprepared('SET IDENTITY_INSERT tc_inits ON');
            TcInit::insert($tcInitData);
            DB::unprepared('SET IDENTITY_INSERT tc_inits OFF');
        }

        if (!empty($tcCallusObsData)) {
            TcCallusOb::insert($tcCallusObsData);
        }

        if (!empty($tcInitBottleData)) {
            $batchSize = 100;
            $chunks = array_chunk($tcInitBottleData, $batchSize);
            foreach ($chunks as $chunk) {
                TcInitBottle::insert($chunk);
            }
        }
    }

    public function cekNotExistData($data)
    {
        $importID = array_column($data, 'tc_sample_id');

        $foundIds = TcSample::query()
            ->whereIn('id', $importID)
            ->pluck('id')
            ->toArray();

        if(count($importID) != count($foundIds)){
            $notFoundIds = array_diff($importID, $foundIds);
            self::$error = "Pada data excel ada Sample ID yang tidak valid, yaitu ID = " . (implode(', ', $notFoundIds));
            return true;
        }

        return false;
    }


}
