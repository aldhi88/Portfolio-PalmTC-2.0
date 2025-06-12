<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME').$data['title'] }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ $data['desc'] }}" />
    <meta name="keywords" content="{{ $data['desc'] }}">
    <meta name="author" content="{{ env('APP_AUTHOR') }}" />
    <link rel="icon" href="{{ env('PORTAL_URL').'/images/logo.png' }}" type="image/x-icon">
</head>
<style>
table.minimalistBlack {
  font-family: Arial, Helvetica, sans-serif;
  width: 100%;
  text-align: left;
  border-collapse: collapse;
}
table.minimalistBlack td, table.minimalistBlack th {
  border: 1px solid #878787;
  padding: 4px 3px;
}
table.minimalistBlack tbody td {
  font-size: 12px;
}
table.minimalistBlack thead {
  background: #BCCF9A;
}
table.minimalistBlack thead th {
  font-size: 12px;
  font-weight: bold;
  color: #000000;
  text-align: center;
}
table.minimalistBlack tfoot {
  font-size: 12px;
  font-weight: bold;
  color: #000000;
}
table.minimalistBlack tfoot td {
  font-size: 12px;
}
.text-center{
    text-align: center;
}
</style>
<body>

    <table class="minimalistBlack">
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Week</th>
                <th>Date</th>
                <th>Sampling</th>
                <th>Cross</th>
                <th>Family</th>
                <th>Female<br>Genitor</th>
                <th>Male<br>Genitor</th>
                <th>Block</th>
                <th>Row</th>
                <th>Palm</th>
                <th>Planting<br>Year</th>
                <th>Type</th>
                <th>Program</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['samples'] as $item)
                <tr>
                    <td class="text-center">{{ $item->year }}</td>
                    <td class="text-center">{{ $item->month }}</td>
                    <td class="text-center">{{ $item->weekOfYear }}</td>
                    <td class="text-center">{{ $item->created_at_num_format }}</td>
                    <td class="text-center">{{ $item->sample_number_display }}</td>
                    <td class="text-center">{{ $item->master_treefile->noseleksi }}</td>
                    <td class="text-center">{{ $item->master_treefile->family }}</td>
                    <td class="text-center">{{ $item->master_treefile->indukbet }}</td>
                    <td class="text-center">{{ $item->master_treefile->indukjan }}</td>
                    <td class="text-center">{{ $item->master_treefile->blok }}</td>
                    <td class="text-center">{{ $item->master_treefile->baris }}</td>
                    <td class="text-center">{{ $item->master_treefile->pokok }}</td>
                    <td class="text-center">{{ $item->master_treefile->tahuntanam }}</td>
                    <td class="text-center">{{ $item->master_treefile->tipe }}</td>
                    <td class="text-center">{{ $item->program }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
