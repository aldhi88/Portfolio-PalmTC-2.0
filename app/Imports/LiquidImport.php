<?php

namespace App\Imports;

use App\Models\TcInit;
use App\Models\TcLiquidBottle;
use App\Models\TcLiquidOb;
use App\Models\TcLiquidObDetail;
use App\Models\TcLiquidTransaction;
use App\Models\TcLiquidTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class LiquidImport implements ToCollection
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

            $dtTcLiquidBottle[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'tc_laminar_id' => 99,
                'tc_bottle_id' => $value[1],
                'sub' => 1,
                'type' => 'Suspension',
                'alpha' => 'A',
                'cycle' => 0,
                'bottle_count' => $value[2],
                'status' => 1,
                'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcLiquidObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'alpha' => 'A',
                'cycle' => 0,
                'total_bottle_liquid' => $value[4],
                'total_bottle_oxidate' => $value[5],
                'total_bottle_contam' => $value[6],
                'total_bottle_other' => $value[7],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcLiquidObsDetail[] = [
                'tc_init_id' => $value[0],
                'bottle_liquid' => $value[4],
                'bottle_oxidate' => $value[5],
                'bottle_contam' => $value[6],
                'bottle_other' => $value[7],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcLiquidTransferBottle[] = [
                'tc_init_id' => $value[0],
                'bottle_liquid' => $value[4],
                'bottle_left' => $value[4],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcLiquidObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_bottle_liquid' => 0,
                'total_bottle_oxidate' => 0,
                'total_bottle_contam' => 0,
                'total_bottle_other' => 0,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $dtTcLiquidTransaction[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'first_total' => $value[2],
                'last_total' => $value[2] - ($value[5]+$value[6]+$value[7]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcLiquidBottle = TcLiquidBottle::create($dtTcLiquidBottle[$i]);
            $qTcLiquidOb = TcLiquidOb::create($dtTcLiquidObs[$i]);
            TcLiquidOb::create($dtTcLiquidObsTemp[$i]);

            $dtTcLiquidObsDetail[$i]['tc_liquid_ob_id'] = $qTcLiquidOb->id;
            $dtTcLiquidObsDetail[$i]['tc_liquid_bottle_id'] = $qTcLiquidBottle->id;
            TcLiquidObDetail::create($dtTcLiquidObsDetail[$i]);

            $dtTcLiquidTransaction[$i]['tc_liquid_ob_id'] = $qTcLiquidOb->id;
            $dtTcLiquidTransaction[$i]['tc_liquid_bottle_id'] = $qTcLiquidBottle->id;
            TcLiquidTransaction::create($dtTcLiquidTransaction[$i]);

            $dtTcLiquidTransferBottle[$i]['tc_liquid_ob_id'] = $qTcLiquidOb->id;
            $dtTcLiquidTransferBottle[$i]['tc_liquid_bottle_id'] = $qTcLiquidBottle->id;
            TcLiquidTransferBottle::create($dtTcLiquidTransferBottle[$i]);
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
