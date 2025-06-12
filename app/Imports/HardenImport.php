<?php

namespace App\Imports;

use App\Models\TcHarden;
use App\Models\TcInit;
use App\Models\TcHardenOb;
use App\Models\TcHardenObDetail;
use App\Models\TcHardenTree;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class HardenImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
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
                is_null($value[4]) || $value[4]===''
            ){
                self::$error = "Pada data excel ada data value yang kosong. Cek baris ke- " . ($key + 1);
                return;
            }

            if($value[1] < ($value[3]+$value[4])){
                self::$error = "Jumlah Tree tidak boleh kurang dari total Transfer+Death. Cek baris ke- " . ($key + 1);
                return;
            }

            $initID[] = $value[0];

            $dtTcHardens[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'sub' => 1,
                'type' => 'Suspension',
                'alpha' => 'A',
                'status' => 1,
                'tree_date' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
            ];

            for ($i=1; $i <= $value[1]; $i++) {
                $dtTemp[] = [
                    'tc_init_id' => $value[0],
                    'index_number' => $i,
                    'status' => 1
                ];
            }

            $dtTcHardenTree[] = $dtTemp;
            unset($dtTemp);

            $dtTcHardenObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'total_transfer' => $value[3],
                'total_death' => $value[4],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
                'status' => 1,
            ];
            $dtTcHardenObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_transfer' => 0,
                'total_death' => 0,
                'status' => 0,
            ];


            for ($i=0; $i < $value[3]; $i++) {
                $dtTcHardenObDetail[$key-1][] = [
                    'tc_init_id' => $value[0],
                    'is_death' => 0,
                    'pre_nursery' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
                    'is_transfer' => 1,
                    'status' => 0,
                ];
            }

            for ($i=0; $i < $value[4]; $i++) {
                $dtTcHardenObDetail[$key-1][] = [
                    'tc_init_id' => $value[0],
                    'is_death' => 1,
                    'tc_death_id' => 1,
                    'pre_nursery' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
                    'is_transfer' => 0,
                    'status' => 0,
                ];
            }


        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcHardens = TcHarden::create($dtTcHardens[$i]);
            $hardenId = $qTcHardens->id;

            foreach ($dtTcHardenTree[$i] as $key => $value) {
                $value['tc_harden_id'] = $hardenId;
                $q = TcHardenTree::create($value);
                $treeId[$i][$key] = $q->id;
            }

            $dtTcHardenObs[$i]['tc_harden_id'] = $hardenId;
            $qTcHardenOb = TcHardenOb::create($dtTcHardenObs[$i]);
            $obId[] = $qTcHardenOb->id;
            $dtTcHardenObsTemp[$i]['tc_harden_id'] = $hardenId;
            TcHardenOb::create($dtTcHardenObsTemp[$i]);
        }

        // dump($obId, $treeId);

        for ($a=0; $a < count($obId); $a++) {
            for ($i=0; $i < count($dtTcHardenObDetail[$a]); $i++) {
                $dtTcHardenObDetail[$a][$i]['tc_harden_ob_id'] = $obId[$a];
                $dtTcHardenObDetail[$a][$i]['tc_harden_tree_id'] = $treeId[$a][$i];
            }
            // dump($dtTcHardenObDetail[$a]);
            foreach ($dtTcHardenObDetail[$a] as $key => $value) {
                TcHardenObDetail::create($value);
            }
        }

        // dd(0);




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
