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
    border: 1px solid #acacac;
    padding: 4px 3px;
    }
    table.minimalistBlack thead.thead-title th {
    border: 1px solid rgb(110, 161, 255);
    padding: 4px 3px;
    }
    table.minimalistBlack tbody td {
    font-size: 12px;
    padding-top: 11px;
    padding-bottom: 10px;
    }
    table.minimalistBlack thead {
    background: #eeeeee;
    }
    table.minimalistBlack thead.thead-title {
    background: #b0cbff;
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

    <table class="minimalistBlack" style="margin-top: 10px">
        <thead>
            <tr>
                <th rowspan="2">Program</th>
                <th rowspan="2">Sample</th>
                <th rowspan="2">Bottle<br>Date</th>
                <th rowspan="2">Alpha</th>
                <th rowspan="2">Cycle</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Total</th>
                <th colspan="6">Observation</th>
            </tr>
            <tr>
                <th>Worker</th>
                <th width="80">Date</th>
                <th>Liquid</th>
                <th>Oxidate</th>
                <th>Contam</th>
                <th>Other</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['bottles'] as $item)
                <tr class="text-center">
                    <td>{{ $item->tc_inits->tc_samples->program }}</td>
                    <td>{{ $item->tc_inits->tc_samples->sample_number_display }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date_work)->format('d//m/Y') }}</td>
                    <td>{{ $item->alpha }}</td>
                    <td>{{ $item->cycle }}</td>
                    <td>{{ $item->tc_workers->code }}</td>
                    <td>{{ $item->bottle_count }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
