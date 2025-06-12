<?php

namespace App\Imports;

use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoList;
use App\Models\TcEmbryoOb;
use App\Models\TcEmbryoObDetail;
use App\Models\TcEmbryoTransferBottle;
use App\Models\TcInit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmbryoImport implements ToCollection
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

            $dtTcEmbryoBottle[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'tc_laminar_id' => 99,
                'sub' => $value[1],
                'number_of_bottle' => $value[2],
                'status' => 1,
                'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $dtTcEmbryoObs[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'sub' => 1,
                'total_bottle_embryo' => $value[4],
                'total_bottle_oxidate' => $value[5],
                'total_bottle_contam' => $value[6],
                'total_bottle_other' => $value[7],
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $dtTcEmbryoObsDetail[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'bottle_embryo' => $value[4],
                'bottle_oxidate' => $value[5],
                'bottle_contam' => $value[6],
                'bottle_other' => $value[7],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $dtTcEmbryoTransferBottle[] = [
                'tc_init_id' => $value[0],
                'tc_worker_id' => 99,
                'bottle_embryo' => $value[4],
                'bottle_left' => $value[4],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $dtTcEmbryoObsTemp[] = [
                'tc_init_id' => $value[0],
                'total_bottle_embryo' => 0,
                'total_bottle_oxidate' => 0,
                'total_bottle_contam' => 0,
                'total_bottle_other' => 0,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $dtTcEmbryoList[] = [
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

            $qTcEmbryoBottle = TcEmbryoBottle::create($dtTcEmbryoBottle[$i]);
            $qTcEmbryoOb = TcEmbryoOb::create($dtTcEmbryoObs[$i]);
            TcEmbryoOb::create($dtTcEmbryoObsTemp[$i]);

            $dtTcEmbryoObsDetail[$i]['tc_embryo_ob_id'] = $qTcEmbryoOb->id;
            $dtTcEmbryoObsDetail[$i]['tc_embryo_bottle_id'] = $qTcEmbryoBottle->id;
            TcEmbryoObDetail::create($dtTcEmbryoObsDetail[$i]);

            $dtTcEmbryoList[$i]['tc_embryo_ob_id'] = $qTcEmbryoOb->id;
            $dtTcEmbryoList[$i]['tc_embryo_bottle_id'] = $qTcEmbryoBottle->id;
            TcEmbryoList::create($dtTcEmbryoList[$i]);

            $dtTcEmbryoTransferBottle[$i]['tc_embryo_ob_id'] = $qTcEmbryoOb->id;
            $dtTcEmbryoTransferBottle[$i]['tc_embryo_bottle_id'] = $qTcEmbryoBottle->id;
            TcEmbryoTransferBottle::create($dtTcEmbryoTransferBottle[$i]);
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
