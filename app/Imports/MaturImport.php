<?php

namespace App\Imports;

use App\Models\TcInit;
use App\Models\TcMaturBottle;
use App\Models\TcMaturOb;
use App\Models\TcMaturObDetail;
use App\Models\TcMaturTransaction;
use App\Models\TcMaturTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MaturImport implements ToCollection
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

            $dtTcMaturBottle[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'tc_laminar_id' => 99,
                'tc_bottle_id' => $value[1],
                'sub' => 1,
                'type' => 'Suspension',
                'alpha' => 'A',
                'bottle_count' => $value[2],
                'status' => 1,
                'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcMaturObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'alpha' => 'A',
                'total_bottle_matur' => $value[4],
                'total_bottle_oxidate' => $value[5],
                'total_bottle_contam' => $value[6],
                'total_bottle_other' => $value[7],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcMaturObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_bottle_matur' => 0,
                'total_bottle_oxidate' => 0,
                'total_bottle_contam' => 0,
                'total_bottle_other' => 0,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcMaturObsDetail[] = [
                'tc_init_id' => $value[0],
                'bottle_matur' => $value[4],
                'bottle_oxidate' => $value[5],
                'bottle_contam' => $value[6],
                'bottle_other' => $value[7],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcMaturTransaction[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'first_total' => $value[2],
                'last_total' => $value[2] - ($value[5]+$value[6]+$value[7]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcMaturTransferBottle[] = [
                'tc_init_id' => $value[0],
                'bottle_matur' => $value[4],
                'bottle_left' => $value[4],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcMaturBottle = TcMaturBottle::create($dtTcMaturBottle[$i]);
            $qTcMaturOb = TcMaturOb::create($dtTcMaturObs[$i]);
            TcMaturOb::create($dtTcMaturObsTemp[$i]);

            $dtTcMaturObsDetail[$i]['tc_matur_ob_id'] = $qTcMaturOb->id;
            $dtTcMaturObsDetail[$i]['tc_matur_bottle_id'] = $qTcMaturBottle->id;
            TcMaturObDetail::create($dtTcMaturObsDetail[$i]);

            $dtTcMaturTransaction[$i]['tc_matur_ob_id'] = $qTcMaturOb->id;
            $dtTcMaturTransaction[$i]['tc_matur_bottle_id'] = $qTcMaturBottle->id;
            TcMaturTransaction::create($dtTcMaturTransaction[$i]);

            $dtTcMaturTransferBottle[$i]['tc_matur_ob_id'] = $qTcMaturOb->id;
            $dtTcMaturTransferBottle[$i]['tc_matur_bottle_id'] = $qTcMaturBottle->id;
            TcMaturTransferBottle::create($dtTcMaturTransferBottle[$i]);
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
