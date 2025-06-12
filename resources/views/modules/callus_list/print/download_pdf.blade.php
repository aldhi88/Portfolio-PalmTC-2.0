<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME').$title }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ $desc }}" />
    <meta name="keywords" content="{{ $desc }}">
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
                <th class="text-center">Sampling</th>
                <th>Sampling Date</th>                 
                <th>Total Explant</th>
                <th>Reacting Explant</th>
                <th>% Callogenesis</th>
                <th>58 Flask (nbr)</th>
                <th>Type</th>
                <th>Program</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($samples as $item)
                <tr>
                  <td class="text-center">{{ $item['sampling'] }}</td>
                  <td class="text-center">{{ $item['sampling_date'] }}</td>
                  <td class="text-center">{{ $item['total_explant'] }}</td>
                  <td class="text-center">{{ $item['total_explant_callus'] }}</td>
                  <td class="text-center">{{ $item['persen_explant_callus'] }}</td>
                  <td class="text-center">{{ $item['total_bottle_callus'] }}</td>
                  <td class="text-center">{{ $item['type'] }}</td>
                  <td class="text-center">{{ $item['program'] }}</td>
                  <td class="text-center">{{ $item['remarks'] }}</td>
              </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
