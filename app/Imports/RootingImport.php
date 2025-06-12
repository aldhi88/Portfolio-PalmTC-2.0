<?php

namespace App\Imports;

use App\Models\TcInit;
use App\Models\TcRootingBottle;
use App\Models\TcRootingOb;
use App\Models\TcRootingObDetail;
use App\Models\TcRootingTransaction;
use App\Models\TcRootingTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RootingImport implements ToCollection
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
                is_null($value[4]) || $value[4]==='' ||
                is_null($value[5]) || $value[5]==='' ||
                is_null($value[6]) || $value[6]==='' ||
                is_null($value[7]) || $value[7]===''
            ){
                self::$error = "Pada data excel ada data value yang kosong. Cek baris ke- " . ($key + 1);
                return;
            }

            $initID[] = $value[0];

            $dtTcRootingBottle[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'tc_laminar_id' => 99,
                'tc_bottle_id' => $value[1],
                'sub' => 1,
                'bottle_type' => 'Suspension',
                'type' => 1,
                'alpha' => 'A',
                'bottle_count' => ceil($value[2]/2),
                'leaf_count' => $value[2],
                'status' => 1,
                'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcRootingObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'alpha' => 'A',
                'total_bottle_rooting' => ceil($value[4]/2),
                'total_leaf_rooting' => $value[4],
                'total_bottle_oxidate' => ceil($value[5]/2),
                'total_leaf_oxidate' => $value[5],
                'total_bottle_contam' => ceil($value[6]/2),
                'total_leaf_contam' => $value[6],
                'total_bottle_other' => ceil($value[7]/2),
                'total_leaf_other' => $value[7],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcRootingObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_bottle_rooting' => 0,
                'total_leaf_rooting' => 0,
                'total_bottle_oxidate' => 0,
                'total_leaf_oxidate' => 0,
                'total_bottle_contam' => 0,
                'total_leaf_contam' => 0,
                'total_bottle_other' => 0,
                'total_leaf_other' => 0,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcRootingObsDetail[] = [
                'tc_init_id' => $value[0],
                'bottle_rooting' => ceil($value[4]/2),
                'leaf_rooting' => $value[4],
                'bottle_oxidate' => ceil($value[5]/2),
                'leaf_oxidate' => $value[5],
                'bottle_contam' => ceil($value[6]/2),
                'leaf_contam' => $value[6],
                'bottle_other' => ceil($value[7]/2),
                'leaf_other' => $value[7],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcRootingTransaction[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'first_total' => ceil($value[2]/2),
                'first_leaf' => $value[2],
                'last_total' => ceil($value[2]/2) - ( ceil($value[5]/2) + ceil($value[6]/2) + ceil($value[7]/2)),
                'last_leaf' => $value[2] - ($value[5]+$value[6]+$value[7]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcRootingTransferBottle[] = [
                'tc_init_id' => $value[0],
                'bottle_rooting' => ceil($value[4]/2),
                'leaf_rooting' => $value[4],
                'bottle_left' => ceil($value[4]/2),
                'leaf_left' => $value[4],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcRootingBottle = TcRootingBottle::create($dtTcRootingBottle[$i]);
            $qTcRootingOb = TcRootingOb::create($dtTcRootingObs[$i]);
            TcRootingOb::create($dtTcRootingObsTemp[$i]);

            $dtTcRootingObsDetail[$i]['tc_rooting_ob_id'] = $qTcRootingOb->id;
            $dtTcRootingObsDetail[$i]['tc_rooting_bottle_id'] = $qTcRootingBottle->id;
            TcRootingObDetail::create($dtTcRootingObsDetail[$i]);

            $dtTcRootingTransaction[$i]['tc_rooting_ob_id'] = $qTcRootingOb->id;
            $dtTcRootingTransaction[$i]['tc_rooting_bottle_id'] = $qTcRootingBottle->id;
            TcRootingTransaction::create($dtTcRootingTransaction[$i]);

            $dtTcRootingTransferBottle[$i]['tc_rooting_ob_id'] = $qTcRootingOb->id;
            $dtTcRootingTransferBottle[$i]['tc_rooting_bottle_id'] = $qTcRootingBottle->id;
            TcRootingTransferBottle::create($dtTcRootingTransferBottle[$i]);
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
