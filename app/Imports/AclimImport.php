<?php

namespace App\Imports;

use App\Models\TcAclim;
use App\Models\TcInit;
use App\Models\TcAclimOb;
use App\Models\TcAclimObDetail;
use App\Models\TcAclimTree;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AclimImport implements ToCollection
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

            $dtTcAclims[] = [
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

            $dtTcAclimTree[] = $dtTemp;
            unset($dtTemp);

            $dtTcAclimObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'total_transfer' => $value[3],
                'total_death' => $value[4],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
                'status' => 1,
            ];
            $dtTcAclimObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_transfer' => 0,
                'total_death' => 0,
                'status' => 0,
            ];


            for ($i=0; $i < $value[3]; $i++) {
                $dtTcAclimObDetail[$key-1][] = [
                    'tc_init_id' => $value[0],
                    'is_death' => 0,
                    'is_transfer' => 1,
                    'status' => 0,
                ];
            }

            for ($i=0; $i < $value[4]; $i++) {
                $dtTcAclimObDetail[$key-1][] = [
                    'tc_init_id' => $value[0],
                    'is_death' => 1,
                    'tc_death_id' => 1,
                    'is_transfer' => 0,
                    'status' => 0,
                ];
            }


        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcAclims = TcAclim::create($dtTcAclims[$i]);
            $aclimId = $qTcAclims->id;

            foreach ($dtTcAclimTree[$i] as $key => $value) {
                $value['tc_aclim_id'] = $aclimId;
                $q = TcAclimTree::create($value);
                $treeId[$i][$key] = $q->id;
            }

            $dtTcAclimObs[$i]['tc_aclim_id'] = $aclimId;
            $qTcAclimOb = TcAclimOb::create($dtTcAclimObs[$i]);
            $obId[] = $qTcAclimOb->id;
            $dtTcAclimObsTemp[$i]['tc_aclim_id'] = $aclimId;
            TcAclimOb::create($dtTcAclimObsTemp[$i]);
        }

        // dump($obId, $treeId);

        for ($a=0; $a < count($obId); $a++) {
            for ($i=0; $i < count($dtTcAclimObDetail[$a]); $i++) {
                $dtTcAclimObDetail[$a][$i]['tc_aclim_ob_id'] = $obId[$a];
                $dtTcAclimObDetail[$a][$i]['tc_aclim_tree_id'] = $treeId[$a][$i];
            }
            // dump($dtTcAclimObDetail[$a]);
            foreach ($dtTcAclimObDetail[$a] as $key => $value) {
                TcAclimObDetail::create($value);
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
