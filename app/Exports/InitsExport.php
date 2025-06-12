<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InitsExport implements
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
            'tc_sample_id',
            'tc_room_id',
            'number_of_block',
            'number_of_bottle',
            'number_of_plant',
            'desc',
            'created_at',
        ];
    }
    public function collection()
    {
        return new Collection([
            [
                "Isi dengan ID pada menu [Sample -> All Data], ex: 32 (wajib isi)",
                "Isi dengan ID pada menu [Room], ex: 2 (wajib isi)",
                "Isi dengan jumlah block, ex:60",
                "Isi dengan jumlah bottle/block, ex:8",
                "Isi dengan jumlah explant/bottle, ex:3",
                "Ketarangan (boleh kosong)",
                "Isi tanggal tapi formatnya sebagai text bukan tanggal, ex: 31/02/2023, tambahkan tanda kutip 1 ('31/02/2023) untuk menghindari auto format pada excel"
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
