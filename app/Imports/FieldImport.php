<?php

namespace App\Imports;

use App\Models\TcField;
use App\Models\TcInit;
use App\Models\TcFieldOb;
use App\Models\TcFieldObDetail;
use App\Models\TcFieldTree;
use App\Models\TcPlantation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class FieldImport implements ToCollection
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
                is_null($value[3]) || $value[3]==='' ||
                is_null($value[4]) || $value[4]==='' ||
                is_null($value[5]) || $value[5]==='' ||
                is_null($value[6]) || $value[6]===''
            ){
                self::$error = "Pada data excel ada data value yang kosong. Cek baris ke- " . ($key + 1);
                return;
            }

            $plantId = $dtPlant->firstWhere('code', $value[6])['id'] ?? $dtPlant->first()->id;

            $initID[] = $value[0];

            $dtTcFields[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'block' => $value[2],
                'row' => $value[3],
                'tree' => $value[4],
                'tc_plantation_id' => $plantId,
                'sub' => 1,
                'type' => 'Suspension',
                'alpha' => 'A',
                'tree_date' => Carbon::createFromFormat('d/m/Y', $value[6])->format('Y-m-d'),
            ];

            for ($i=1; $i <= $value[1]; $i++) {
                $dtTemp[] = [
                    'tc_init_id' => $value[0],
                    'index_number' => $i,
                ];
            }

            $dtTcFieldTree[] = $dtTemp;
            unset($dtTemp);

            // $dtTcFieldObs[] = [
            //     'tc_init_id' => $value[0],
            //     'tc_worker_id' => 99,
            //     'total_transfer' => $value[8],
            //     'total_death' => $value[9],
            //     'ob_date' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
            //     'status' => 1,
            // ];
            // $dtTcFieldObsTemp[] = [
            //     'tc_init_id' => $value[0],
            //     'total_transfer' => 0,
            //     'total_death' => 0,
            // ];


            // for ($i=0; $i < $value[8]; $i++) {
            //     $dtTcFieldObDetail[$key-1][] = [
            //         'tc_init_id' => $value[0],
            //         'is_death' => 0,
            //         'pre_fieldsery' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
            //         'is_transfer' => 1,
            //     ];
            // }

            // for ($i=0; $i < $value[9]; $i++) {
            //     $dtTcFieldObDetail[$key-1][] = [
            //         'tc_init_id' => $value[0],
            //         'is_death' => 1,
            //         'tc_death_id' => 1,
            //         'pre_fieldsery' => Carbon::createFromFormat('d/m/Y', $value[7])->format('Y-m-d'),
            //         'is_transfer' => 0,
            //     ];
            // }


        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcFields = TcField::create($dtTcFields[$i]);
            $fieldId = $qTcFields->id;

            foreach ($dtTcFieldTree[$i] as $key => $value) {
                $value['tc_field_id'] = $fieldId;
                $q = TcFieldTree::create($value);
                $treeId[$i][$key] = $q->id;
            }

            // $dtTcFieldObs[$i]['tc_field_id'] = $fieldId;
            // $qTcFieldOb = TcFieldOb::create($dtTcFieldObs[$i]);
            // $obId[] = $qTcFieldOb->id;
            // $dtTcFieldObsTemp[$i]['tc_field_id'] = $fieldId;
            // TcFieldOb::create($dtTcFieldObsTemp[$i]);
        }

        // for ($a=0; $a < count($obId); $a++) {
        //     for ($i=0; $i < count($dtTcFieldObDetail[$a]); $i++) {
        //         $dtTcFieldObDetail[$a][$i]['tc_field_ob_id'] = $obId[$a];
        //         $dtTcFieldObDetail[$a][$i]['tc_field_tree_id'] = $treeId[$a][$i];
        //     }
        //     foreach ($dtTcFieldObDetail[$a] as $key => $value) {
        //         TcFieldObDetail::create($value);
        //     }
        // }

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
