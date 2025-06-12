<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;


class CallusExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'tc_init_id',
            'tc_worker_id',
            'date_ob',
            'bottle_callus',
        ];
    }
    public function collection()
    {
        return new Collection([
            [
                "Isi dengan ID pada menu [Initiation -> All Data], ex: 32 (wajib isi)",
                "Isi dengan ID pada menu [Worker], ex: 2 (wajib isi)",
                "Isi tanggal tapi formatnya sebagai text bukan tanggal, ex: 31/02/2023, tambahkan tanda kutip 1 ('31/02/2023) untuk menghindari auto format pada excel",
                "Isi dengan jumlah bottle callus terakhir, ex:60",
            ]
        ]);
    }
    public function title(): string
    {
        return 'Form';
    }
    public function sheets(): array
    {
        $return = [];
        $return[] = new InitsExport();
        return $return;
    }
}