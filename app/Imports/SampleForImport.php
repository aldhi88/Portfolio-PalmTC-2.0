<?php

namespace App\Imports;

use App\Models\MasterTreefile;
use App\Models\TcSample;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SampleForImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static $error;

    public function collection(Collection $rows)
    {
        self::$error = false;
        foreach ($rows as $key => $value) {
            if ($key === 0) { continue; }
            if (isset($value[0]) && $value[0] == '<end>') {
                break;
            }

            $data[] = [
                'sample_number' => $value[0],
                'master_treefile_id' => $value[1],
                'program' => $value[2],
                'desc' => 'IMPORT DATA',
                'created_at' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                'updated_at' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
            ];
        }

        // Validasi data kosong
        if ($this->cekValueKosong($data)) {
            return;
        }

        // Validasi duplikasi sample_number
        if ($this->cekExistSampleNumber($data)) {
            return;
        }

        // Validasi exist ID master treefile
        if ($this->cekNotExistTreeFile($data)) {
            return;
        }

        // Batasi jumlah data per batch
        $batchSize = 100; // Misalnya 1000 baris per batch
        $chunks = array_chunk($data, $batchSize);

        foreach ($chunks as $chunk) {
            TcSample::insert($chunk);
        }

    }

    public function cekValueKosong($data)
    {
        foreach ($data as $index => $row) {
            foreach ($row as $key => $value) {
                if (empty($value) && $value !== '0') {
                    self::$error = "Pada data excel ada data value yang kosong. Cek baris ke- " . ($index + 1);
                    return true;
                }
            }
        }

        return false;
    }

    public function cekExistSampleNumber($data)
    {
        $sampleNumbers = array_column($data, 'sample_number');
        $existingSamples = TcSample::whereIn('sample_number', $sampleNumbers)
            ->pluck('sample_number')
            ->toArray();

        foreach ($data as $index => $row) {
            if (in_array($row['sample_number'], $existingSamples)) {
                self::$error = "Pada data excel ada Sample Number yang duplikat dengan data di database. Cek baris ke- " . ($index + 1);
                return true;
            }
        }

        return false;
    }

    public function cekNotExistTreeFile($data)
    {
        $masterTreefileIds = array_column($data, 'master_treefile_id');
        $validIds = MasterTreefile::whereIn('id', $masterTreefileIds)
            ->whereNotNull('noseleksi')
            ->pluck('id')
            ->toArray();

        foreach ($data as $index => $row) {
            if (!in_array($row['master_treefile_id'], $validIds)) {
                self::$error = "Pada data excel, ID Master Treefile tidak ditemukan di database. Cek baris ke- " . ($index + 1);
                return true;
            }
        }

        return false;
    }


}
