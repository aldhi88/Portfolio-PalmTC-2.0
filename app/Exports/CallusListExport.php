<?php

namespace App\Exports;

use App\Models\TcCallusObDetail;
use App\Models\TcEmbryoBottle;
use App\Models\TcInit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class CallusListExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function headings(): array{
        return [
            'Sampling',
            'Sampling Date',
            'Total Explant',
            'Reacting Explant',
            '% Callogenesis',
            '58 Flask (nbr)',
            'Type',
            'Program',
            'Remarks',
        ];
    }
    public function map($data): array
    {
        return [
            $data['sampling'],
            $data['sampling_date'],
            $data['total_explant'],
            $data['total_explant_callus'],
            $data['persen_explant_callus'],
            $data['total_bottle_callus'],
            $data['type'],
            $data['program'],
            $data['remarks'],
        ];
    }

    public function collection()
    {
        $from = $this->data['from'].'-01-01 00:00:00';
        $to = $this->data['to'].'-12-31 23:59:59';

        $q = TcInit::select([
                'id','tc_sample_id','number_of_plant',
                DB::raw('
                    number_of_plant * (
                        SELECT COUNT(*) FROM tc_init_bottles WHERE status=1 AND tc_init_id=tc_inits.id
                    ) AS total_explant
                ') //note
            ])
            ->with([
                'tc_samples:id,master_treefile_id,program,created_at,program,sample_number',
                'tc_samples.master_treefile:id,tipe'
            ])
            ->whereHas('tc_samples',function(Builder $query) use($from,$to){
                $query->select('id','master_treefile_id','created_at','sample_number','program')
                    ->whereBetween('created_at',[$from,$to]);
            })
            ->get();
        foreach ($q as $key => $value) {
            $exCallus = TcCallusObDetail::getTotalExplantCallusByInit($value->id);
            $totalAwal = TcEmbryoBottle::where('tc_init_id',$value->id)->sum('number_of_bottle');
            $totalUsed = TcEmbryoBottle::usedBottleByInit($value->id);
            $data[] = [
                'sampling' => $value->tc_samples->sample_number_display,
                'sampling_date' => Carbon::parse($value->tc_samples->created_at)->format('d/m/Y'),
                'total_explant' => $value->total_explant,
                'total_explant_callus' => $exCallus,
                'persen_explant_callus' => number_format($exCallus/$value->total_explant*100,2,',','.'),
                'total_bottle_callus' => $totalAwal - $totalUsed,
                'type' => $value->tc_samples->master_treefile->tipe,
                'program' => $value->tc_samples->program,
                'remarks' => is_null($value->date_stop)?'On Going':Carbon::parse($value->date_stop)->format('d/m/Y'),
            ];
        }
        $data['samples'] = collect($data);
        return $data['samples'];
    }
}
