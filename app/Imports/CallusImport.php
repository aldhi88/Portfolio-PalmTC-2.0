<?php

namespace App\Imports;

use App\Models\TcCallusOb;
use App\Models\TcCallusObDetail;
use App\Models\TcInit;
use App\Models\TcInitBottle;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class CallusImport implements ToCollection
{
    public static $error;

    public function collection(Collection $rows)
    {
        self::$error = false;

        foreach ($rows as $key => $value) {
            if ($key === 0) { continue; }

            if (isset($value[0]) && $value[0] === '<end>') {
                break;
            }

            if(
                is_null($value[0]) || $value[0]==='' ||
                is_null($value[1]) || $value[1]==='' ||
                is_null($value[2]) || $value[2]==='' ||
                is_null($value[3]) || $value[3]==='' ||
                is_null($value[4]) || $value[4]==='' ||
                is_null($value[5]) || $value[5]===''
            ){
                self::$error = "Pada data excel ada data value yang kosong. Cek baris ke- " . ($key + 1);
                return;
            }

            if(($value[2]+$value[4]+$value[5])>480){
                self::$error = "Jumlah botol melebihi batas maksimum, total botol callus, oksidasi dan kontaminasi maksimal 480. Cek baris ke- " . ($key + 1);
                return;
            }

            $initID[] = $value[0];

            $dtExcel[] = [
                'id_init' => $value[0],
                'tgl_obs' => Carbon::createFromFormat('d/m/Y', $value[1])->format('Y-m-d'),
                'botol_callus' => $value[2],
                'daun_callus' => $value[3],
                'botol_oksi' => $value[4],
                'botol_kontam' => $value[5]
            ];

        }

        if (!$this->isForeignKeyExist($initID)) {
            return;
        }

        foreach ($dtExcel as $key => $value) {

            //prepare data obs yang di update
            $data = [
                'tc_worker_id' => 99,
                'date_schedule' => $value['tgl_obs'],
                'date_ob' => $value['tgl_obs'],
                'status' => 1,
                'bottle_callus' => $value['botol_callus']
            ];

            $qUpCallusOb = TcCallusOb::where('tc_init_id', '=', $value['id_init'])->first();
            $qUpCallusOb->update($data);
            $obsId = $qUpCallusOb->id;

            //prepare data obs baru
            $data = [
                'tc_init_id' => $value['id_init'],
                'date_schedule' => Carbon::parse($data['date_ob'])->addMonths(1),
                'date_ob' => Carbon::parse($data['date_ob'])->addMonths(1),
                'status' => 0,
                'bottle_callus' => 0
            ];
            TcCallusOb::create($data);
            unset($data);

            $jlhBotol = $value['botol_callus'] + $value['botol_oksi'] + $value['botol_kontam'];
            $botol = TcInitBottle::query()
                ->select('id')
                ->where('tc_init_id', $value['id_init'])
                ->orderBy('id', 'ASC')
                ->take($jlhBotol)
                ->get()->toArray();

            $dataWajib = [
                'tc_init_id' => $value['id_init'],
                'tc_callus_ob_id' => $obsId
            ];

            $indexBotol = 0;
            $indexPlant = 1;
            for ($i=0; $i < $value['daun_callus']; $i++) {
                $dt['callus'][$i] = $dataWajib;
                $dt['callus'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                $dt['callus'][$i]['explant_number'] = $indexPlant;
                $dt['callus'][$i]['result'] = 1;
                $dt['callus'][$i]['created_at'] = Carbon::now();
                $dt['callus'][$i]['updated_at'] = Carbon::now();
                if($indexBotol == $value['botol_callus']-1){
                    $indexBotol = 0;
                    $indexPlant++;
                }else{
                    $indexBotol++;
                }
            }
            if($value['daun_callus']>0){
                $chunks = array_chunk($dt['callus'], 100);
                foreach ($chunks as $chunk) {
                    TcCallusObDetail::insert($chunk);
                }
            }
            unset($dt);

            $indexBotol = $value['botol_callus'];
            $indexPlant = 1;
            for ($i=0; $i < $value['botol_oksi']*3; $i++) {
                $dt['oxi'][$i] = $dataWajib;
                $dt['oxi'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                $dt['oxi'][$i]['explant_number'] = $indexPlant;
                $dt['oxi'][$i]['result'] = 2;
                $dt['oxi'][$i]['created_at'] = Carbon::now();
                $dt['oxi'][$i]['updated_at'] = Carbon::now();
                if($indexBotol == $value['botol_oksi']+$value['botol_callus']-1){
                    $indexBotol = $value['botol_callus'];
                    $indexPlant++;
                }else{
                    $indexBotol++;
                }
            }
            if($value['botol_oksi']>0){
                $chunks = array_chunk($dt['oxi'], 100);
                foreach ($chunks as $chunk) {
                    TcCallusObDetail::insert($chunk);
                }
            }
            unset($dt);

            $indexBotol = $value['botol_callus']+$value['botol_oksi'];
            $indexPlant = 1;
            for ($i=0; $i < $value['botol_kontam']*3; $i++) {
                $dt['contam'][$i] = $dataWajib;
                $dt['contam'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                $dt['contam'][$i]['explant_number'] = $indexPlant;
                $dt['contam'][$i]['result'] = 3;
                $dt['contam'][$i]['tc_contamination_id'] = 1;
                $dt['contam'][$i]['created_at'] = Carbon::now();
                $dt['contam'][$i]['updated_at'] = Carbon::now();
                if($indexBotol == $value['botol_kontam']+$value['botol_callus']+$value['botol_oksi']-1){
                    $indexBotol = $value['botol_callus']+$value['botol_oksi'];
                    $indexPlant++;
                }else{
                    $indexBotol++;
                }
            }
            if($value['botol_kontam']>0){
                $chunks = array_chunk($dt['contam'], 100);
                foreach ($chunks as $chunk) {
                    TcCallusObDetail::insert($chunk);
                }
            }
            unset($dt);
        }

    }

    public function notFoundObs($data)
    {
        $foundIds = TcCallusOb::query()
            ->whereIn('tc_init_id', $data)
            ->pluck('tc_init_id')
            ->toArray();

        $notFoundIds = array_diff($data, $foundIds);
        return $notFoundIds;
    }

    public function isForeignKeyExist($data)
    {
        $foundIds = TcInit::query()
            ->whereIn('id', $data)
            ->pluck('id')
            ->toArray();

        if(count($data) != count($foundIds)){
            $notFoundIds = array_diff($data, $foundIds);
            self::$error = "Pada data excel ada Initiation ID yang tidak valid, yaitu ID = " . (implode(', ', $notFoundIds));
            return false;
        }

        return true;
    }
}
