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
            font-weight: bold;
        }
    
        @media print{    
            .no-print, .no-print *
            {
                display: none !important;
            }

            body{
                width: 1.97in;
                height: 0.79in;
            } 

            .a4{
                width: 100% !important;
                height: 100% !important;
            } 
        }

        .a4{
            width: 1.97in;
            margin-top: 20px;
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
            $col = 1;
            $tdWidth = 100/$col;
            $countBottle = count($data["bottles"]);
            $aryFormat = $data["bottles"]->toArray();
        @endphp

        <table class="a4 mx-auto">
            @for ($i=0; $i <= ($countBottle-1); $i++)
                <tr>
                    @for ($x=0;$x<=$col-1;$x++)
                    
                        @php
                            $index = $i++;
                        @endphp

                        @if (array_key_exists($index,$aryFormat))
                            <td class="p-0" style="width:{{ $tdWidth }}%">
                                <div class="text-left">
                                    {{ $data["bottles"][$index]->tc_inits->tc_samples->sample_number_display }}-{{ sprintf('%02d', $data["bottles"][$index]->block_number) }}-{{ sprintf('%03d', $data["bottles"][$index]->bottle_number) }} 
                                    <br>
                                    {{ $data["date_of_work"] }}
                                </div>
                                <div class="row">
                                    {{-- <div class="col">
                                        <p class="my-0">Initiation</p>
                                        {{ $data["bottles"][$index]->tc_medium_stocks->tc_mediums->code }}/{{ $data["bottles"][$index]->tc_medium_stocks->tc_agars->code }}/{{ $data["bottles"][$index]->tc_workers->code }}
                                        <br>
                                        {{ $data["bottles"][$index]->tc_inits->tc_samples->master_treefile->tahuntanam }}/{{ $data["bottles"][$index]->tc_inits->tc_samples->program }}
                                        <br>
                                        {{ $data["bottles"][$index]->tc_inits->tc_samples->master_treefile->noseleksi }}
                                    </div> --}}
                                    {{-- <div class="col text-right">
                                        {!! 
                                            QrCode::size(80)->generate(
                                                $data["bottles"][$index]->tc_inits->tc_samples->sample_number_display .'-'. sprintf('%02d', $data["bottles"][$index]->block_number) .'-'. sprintf('%03d', $data["bottles"][$index]->bottle_number)
                                            ); 
                                        !!}
                                    </div> --}}
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
