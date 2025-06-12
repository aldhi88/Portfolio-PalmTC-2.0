<?php

namespace App\Imports;

use App\Models\TcNur;
use App\Models\TcInit;
use App\Models\TcNurOb;
use App\Models\TcNurObDetail;
use App\Models\TcNurTree;
use App\Models\TcPlantation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class NurImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public static $error;
    public function collection(Collection $rows)
    {
        self::$error = false;
        $dtPlant = TcPlantation::query()
                ->select('id', 'code')
                ->get();

        foreach ($rows as $key => $value) {

            if ($key === 0) { continue; }

            if (isset($value[0]) && $value[0] === '<end>') {
                break;
            }

            if(
                is_null($value[0]) || $value[0]==='' ||
                is_null($value[1]) || $value[1]==='' ||
                is_null($value[2]) || $value[2]==='' ||
                is_null($value[6]) || $value[6]==='' ||
                is_null($value[7]) || $value[7]===''
            ){
                self::$error = "Pada data excel ada data value yang kosong. Cek baris ke- " . ($key + 1);
                return;
            }

            if($value[1] < ($value[3]+$value[4])){
                self::$error = "Jumlah Tree tidak boleh kurang dari total Transfer+Death. Cek baris ke- " . ($key + 1);
                return;
            }

            if($value[2]==='ESTATE'){
                $value[2] = 2;
            }else{
                $value[2] = 1;
            }

            if(is_null($value[3]) || $value[3]==='' || $value[3]==='0' || $value[3]===0){
                $value[3] = null;
            }
            if(is_null($value[4]) || $value[4]==='' || $value[4]==='0' || $value[4]===0){
                $value[4] = null;
            }
            if(is_null($value[5]) || $value[5]==='' || $value[5]==='0' || $value[5]===0){
                $value[5] = null;
            }

            $plantId = $dtPlant->firstWhere('code', $value[6])['id'] ?? $dtPlant->first()->id;

            $initID[] = $value[0];

            $dtTcNurs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'category' => $value[2],
                'block' => $value[3],
                'row' => $value[4],
                'tree' => $value[5],
                'tc_plantation_id' => $plantId,
                'sub' => 1,
                'type' => 'Suspension',
                'alpha' => 'A',
                'tree_date' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
            ];

            for ($i=1; $i <= $value[1]; $i++) {
                $dtTemp[] = [
                    'tc_init_id' => $value[0],
                    'index_number' => $i,
                ];
            }

            $dtTcNurTree[] = $dtTemp;
            unset($dtTemp);

            $dtTcNurObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'total_transfer' => $value[8],
                'total_death' => $value[9],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
                'status' => 1,
            ];
            $dtTcNurObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_transfer' => 0,
                'total_death' => 0,
            ];


            for ($i=0; $i < $value[8]; $i++) {
                $dtTcNurObDetail[$key-1][] = [
                    'tc_init_id' => $value[0],
                    'is_death' => 0,
                    'pre_nursery' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
                    'is_transfer' => 1,
                ];
            }

            for ($i=0; $i < $value[9]; $i++) {
                $dtTcNurObDetail[$key-1][] = [
                    'tc_init_id' => $value[0],
                    'is_death' => 1,
                    'tc_death_id' => 1,
                    'pre_nursery' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
                    'is_transfer' => 0,
                ];
            }


        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcNurs = TcNur::create($dtTcNurs[$i]);
            $nurId = $qTcNurs->id;

            foreach ($dtTcNurTree[$i] as $key => $value) {
                $value['tc_nur_id'] = $nurId;
                $q = TcNurTree::create($value);
                $treeId[$i][$key] = $q->id;
            }

            $dtTcNurObs[$i]['tc_nur_id'] = $nurId;
            $qTcNurOb = TcNurOb::create($dtTcNurObs[$i]);
            $obId[] = $qTcNurOb->id;
            $dtTcNurObsTemp[$i]['tc_nur_id'] = $nurId;
            TcNurOb::create($dtTcNurObsTemp[$i]);
        }

        for ($a=0; $a < count($obId); $a++) {
            for ($i=0; $i < count($dtTcNurObDetail[$a]); $i++) {
                $dtTcNurObDetail[$a][$i]['tc_nur_ob_id'] = $obId[$a];
                $dtTcNurObDetail[$a][$i]['tc_nur_tree_id'] = $treeId[$a][$i];
            }
            foreach ($dtTcNurObDetail[$a] as $key => $value) {
                TcNurObDetail::create($value);
            }
        }

    }

    public function isForeignKeyInitIDExist($data)
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
