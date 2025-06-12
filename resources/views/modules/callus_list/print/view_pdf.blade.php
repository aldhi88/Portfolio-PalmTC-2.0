<!DOCTYPE html>
<html lang="en">
<head>
    {{-- <title>{{ env('APP_NAME').$data['title'] }}</title> --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    {{-- <meta name="description" content="{{ $data["desc"] }}" />
    <meta name="keywords" content="{{ $data["desc"] }}"> --}}
    <meta name="author" content="{{ env('APP_AUTHOR') }}" />
    <link rel="icon" href="{{ env('PORTAL_URL').'/images/logo.png' }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ env('PORTAL_URL').'/assets/css/style.css' }}">
    <style>
        html, body{
            background-color: white !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    
        @media print{    
            .no-print, .no-print *
            {
                display: none !important;
            }

            body{
                width: 21cm;
                height: 29.7cm;
            } 

            .a4{
                width: 100% !important;
                height: 100% !important;
            } 
        }

        @page { size: landscape; }

        .a4{
            width: 29.7cm;
            height: 21cm;
        } 

    </style>
</head>
<body class="">
<!-- [ Main Content ] start -->
<div class="row p-0 mb-5 mx-0 no-print">
    <div class="col bg-light py-2 text-right shadow-sm">
        <button class="btn btn-primary btn-sm" onclick="window.print();return false;"><i class="feather icon-printer"></i> Print</button>
    </div>
</div>

<div class="row p-0 m-0">
    <div class="col m-0">

        <table class="a4 mx-auto table table-striped table-bordered nowrap table-xs">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Week</th>
                    <th>Date</th>
                    <th>Sampling</th>
                    <th>Cross</th>
                    <th>Family</th>
                    <th>Female Genitor</th>
                    <th>Male Genitor</th>
                    <th>Block</th>
                    <th>Row</th>
                    <th>Palm</th>
                    <th>Planting Year</th>
                    <th>Type</th>
                    <th>Program</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['samples'] as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    </div>
</div>

<!-- Required Js -->
<script src="{{ env('PORTAL_URL').'/assets/js/vendor-all.min.js' }}"></script>

</body>

</html>
