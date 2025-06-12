<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SampleForExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    // WithMapping,
    ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'sample_number',
            'master_treefile_id',
            'program',
            'desc',
            'created_at',
        ];
    }
    public function collection()
    {
        return new Collection([
            [
                "Isi dengan angka, ex: 1,2,3 (wajib isi)",
                "Isi dengan ID pada modul (Tree File), ex: 31453 (wajib isi)",
                "Nama program (boleh kosong)",
                "Keterangan (boleh kosong)",
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
        $return[] = new SampleForExport();
        return $return;
    }
}
