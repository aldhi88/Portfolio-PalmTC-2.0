<?php

namespace App\Exports;

use App\Models\TcSample;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class SamplesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function headings(): array{
        return [
            'Year',
            'Month',
            'Week',
            'Date',
            'Sampling',
            'Cross',
            'Family',
            'Female Genitor',
            'Male Genitor',
            'Block',
            'Row',
            'Palm',
            'Planting Year',
            'Type',
            'Program'
        ];
    }
    public function map($data): array
    {
        return [
            $data->year,
            $data->month,
            $data->weekOfYear,
            $data->created_at_num_format,
            $data->sample_number_display,
            $data->master_treefile->noseleksi,
            $data->master_treefile->family,
            $data->master_treefile->indukbet,
            $data->master_treefile->indukjan,
            $data->master_treefile->blok,
            $data->master_treefile->baris,
            $data->master_treefile->pokok,
            $data->master_treefile->tahuntanam,
            $data->master_treefile->tipe,
            $data->program,
        ];
    }
    public function collection()
    {
        $from = $this->data['from'].'-01-01 00:00:00';
        $to = $this->data['to'].'-12-31 23:59:59';
        $return = collect([]);
        $data['samples'] = TcSample::select(
            DB::raw('
                *,
                YEAR(created_at) as year,
                MONTH(created_at) as month
            '))
            ->whereBetween('created_at',[$from,$to])
            ->orderBy('created_at','asc')
            ->get();


        return $data['samples'];
    }
}
