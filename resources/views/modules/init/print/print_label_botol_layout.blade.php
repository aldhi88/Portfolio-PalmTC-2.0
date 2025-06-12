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
            margin: 0 0 0 -2px !important;
            padding: 0 !important;
            font-weight: bold;
            font-size: 17px;
            line-height: 20px;
        }

        @media print{
            .no-print, .no-print *
            {
                display: none !important;
            }

            body{
                width: 18.8cm;
                height: 15.49cm;
            }

            .a4{
                width: 100% !important;
                height: 100% !important;
            }
        }

        .a4{
            width: 25cm;
            /* height: 29.7cm; */
        }

        thead > tr > td{
            padding: 0px;
        }
        tfoot > tr > td{
            padding: 0px;
            margin: 0px;
        }
        .head{
            margin-top: 10px;
        }
        tbody > tr > td{
            padding: 13px 0px 13px 0px;
            /* border: solid 1px black; */
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
            $col = 6;
            $tdWidth = 100/$col;
            $countBottle = count($data["bottles"]);
            $aryFormat = $data["bottles"]->toArray();
        @endphp

        <table class="a4 mx-auto">
            <thead><tr><td><div class="head"></div></td></tr></thead>
            <tbody>
            @for ($i=0; $i <= ($countBottle-1); $i++)
                <tr>
                    @for ($x=0;$x<=$col-1;$x++)

                        @php
                            $index = $i++;
                        @endphp

                        @if (array_key_exists($index,$aryFormat))
                            <td style="width:{{ $tdWidth }}%">
                                <div class="text-center">
                                    {{ $data["bottles"][$index]->tc_inits->tc_samples->sample_number_display }}<span style="color: red">{{ sprintf('%02d', $data["bottles"][$index]->block_number) }}</span><span style="color: blue">{{ sprintf('%03d', $data["bottles"][$index]->bottle_number) }}</span>{{ $data["bottles"][$index]->tc_medium_stocks->tc_agars->code }}
                                    <br>
                                    {{ $data["bottles"][$index]->tc_workers->code }} {{ \Carbon\Carbon::parse($data["bottles"][$index]->tc_inits->date_work)->format('d.m.y') }}
                                </div>
                            </td>
                        @else
                            <td style="width:{{ $tdWidth }}%">&nbsp;</td>
                        @endif

                    @endfor
                </tr>
                @php
                    $i-=1;
                @endphp
            @endfor
            </tbody>
            <tfoot><tr><td></td></tr></tfoot>
        </table>


    </div>
</div>

<!-- Required Js -->
<script src="{{ env('PORTAL_URL').'/assets/js/vendor-all.min.js' }}"></script>

</body>

</html>
