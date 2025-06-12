<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME').$data['title'] }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ $data["desc"] }}" />
    <meta name="keywords" content="{{ $data["desc"] }}">
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

        .a4{
            width: 21cm;
            /* height: 29.7cm; */
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

        @php
            $col = 3;
            $tdWidth = 100/$col;
            $countBottle = count($data['transfer']);
            $aryFormat = $data["transfer"];
        @endphp

        <table class="a4 mx-auto">
            @for ($i=0; $i <= ($countBottle-1); $i++)
                <tr>
                    @for ($x=0;$x<=$col-1;$x++)
                    
                        @php
                            $index = $i++;
                        @endphp

                        @if (array_key_exists($index,$aryFormat))
                            <td class="border p-3" style="width:{{ $tdWidth }}%">
                                <div class="text-center">
                                    <p class="my-0 py-0">
                                        {{ $data["transfer"][$index]['tc_aclims']['tc_inits']['tc_samples']['sample_number_display'] }} / {{ $data['transfer'][$index]['index_number'] }}
                                    </p>
                                    <p class="my-0 py-0">
                                        {{ \Carbon\Carbon::parse($data["transfer"][$index]['tc_aclims']['tree_date'])->format('d-m-Y') }}
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="col-8 px-1">
                                        <p class="my-0 py-0">Acclimatization</p>
                                        <p class="my-0 py-0">
                                            {{$data["transfer"][$index]['tc_aclims']['alpha']}}/{{ $data["transfer"][$index]['tc_aclims']['tc_workers']['code'] }}
                                        </p>
                                    </div>
                                    <div class="col text-right pr-1 pl-0">
                                        {!! 
                                            QrCode::size(75)->generate(
                                                $data["transfer"][$index]['tc_aclims']['tc_inits']['tc_samples']['sample_number']
                                            ); 
                                        !!}
                                    </div>
                                </div>
                            </td>
                        @else
                            <td class="border p-3" style="width:{{ $tdWidth }}%">&nbsp;</td>
                        @endif

                    @endfor
                </tr>
                @php
                    $i-=1;
                @endphp
            @endfor
        </table>


    </div>
</div>

<!-- Required Js -->
<script src="{{ env('PORTAL_URL').'/assets/js/vendor-all.min.js' }}"></script>

</body>

</html>
