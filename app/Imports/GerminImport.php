<?php

namespace App\Imports;

use App\Models\TcInit;
use App\Models\TcGerminBottle;
use App\Models\TcGerminOb;
use App\Models\TcGerminObDetail;
use App\Models\TcGerminTransaction;
use App\Models\TcGerminTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GerminImport implements ToCollection
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

            $dtTcGerminBottle[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'tc_laminar_id' => 99,
                'tc_bottle_id' => $value[1],
                'sub' => 1,
                'type' => $value[8],
                'alpha' => 'A',
                'bottle_count' => $value[2],
                'status' => 1,
                'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcGerminObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'alpha' => 'A',
                'total_bottle_germin' => $value[4],
                'total_bottle_oxidate' => $value[5],
                'total_bottle_contam' => $value[6],
                'total_bottle_other' => $value[7],
                'ob_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcGerminObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_bottle_germin' => 0,
                'total_bottle_oxidate' => 0,
                'total_bottle_contam' => 0,
                'total_bottle_other' => 0,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcGerminObsDetail[] = [
                'tc_init_id' => $value[0],
                'bottle_germin' => $value[4],
                'bottle_oxidate' => $value[5],
                'bottle_contam' => $value[6],
                'bottle_other' => $value[7],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcGerminTransaction[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'first_total' => $value[2],
                'last_total' => $value[2] - ($value[5]+$value[6]+$value[7]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $dtTcGerminTransferBottle[] = [
                'tc_init_id' => $value[0],
                'bottle_germin' => $value[4],
                'bottle_left' => $value[4],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if (!$this->isForeignKeyInitIDExist($initID)) {
            return;
        }

        for ($i=0; $i < count($initID); $i++) {

            $qTcGerminBottle = TcGerminBottle::create($dtTcGerminBottle[$i]);
            $qTcGerminOb = TcGerminOb::create($dtTcGerminObs[$i]);
            TcGerminOb::create($dtTcGerminObsTemp[$i]);

            $dtTcGerminObsDetail[$i]['tc_germin_ob_id'] = $qTcGerminOb->id;
            $dtTcGerminObsDetail[$i]['tc_germin_bottle_id'] = $qTcGerminBottle->id;
            TcGerminObDetail::create($dtTcGerminObsDetail[$i]);

            $dtTcGerminTransaction[$i]['tc_germin_ob_id'] = $qTcGerminOb->id;
            $dtTcGerminTransaction[$i]['tc_germin_bottle_id'] = $qTcGerminBottle->id;
            TcGerminTransaction::create($dtTcGerminTransaction[$i]);

            $dtTcGerminTransferBottle[$i]['tc_germin_ob_id'] = $qTcGerminOb->id;
            $dtTcGerminTransferBottle[$i]['tc_germin_bottle_id'] = $qTcGerminBottle->id;
            TcGerminTransferBottle::create($dtTcGerminTransferBottle[$i]);
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
