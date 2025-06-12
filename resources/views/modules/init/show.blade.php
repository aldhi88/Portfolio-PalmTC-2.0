@extends('layouts.master')


@section('css')
    @include('modules.init.include.show_css')
@endsection

@section('js')
    @include('modules.init.include.show_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
        <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
    </div>

    <div class="card-body">
        <div id="step1">
            <div class="row align-items-center">
                <div class="col">
                    <h5>
                        <span class="badge badge-light-primary rounded-0 w-100 py-2">Initiation Data</span>
                    </h5>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="bg-light border px-3 pb-0 pt-2">

                        <div class="row">
                            <div class="col text-center">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group mb-1 border-right border-bottom">
                                            <label><strong>Sampling: </strong></label>
                                            <span>{{ $data['initiations']->tc_samples->sample_number_display }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-1 border-right border-bottom">
                                            <label><strong>Initiation Date: </strong></label>
                                            <span>{{ $data['initiations']->created_at_long_format }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-1 border-bottom">
                                            <label><strong>Working Date: </strong></label>
                                            <span>{{ $data['initiations']->date_work_format }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-center">
                                        <div class="form-group mb-0 border-right">
                                            <label><strong>Block: </strong></label>
                                            <span>{{ $data['initiations']->number_of_block }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-0 border-right">
                                            <label><strong>Bottle/Block: </strong></label>
                                            <span>{{ $data['initiations']->number_of_bottle }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-0 border-right">
                                            <label><strong>Plant/Bottle: </strong></label>
                                            <span>{{ $data['initiations']->number_of_plant }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-0">
                                            <label><strong>Room: </strong></label>
                                            <span>{{ $data['initiations']->tc_rooms->code }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div id="step2">
            <div class="row align-items-center mt-3">
                <div class="col">
                    <h5>
                        <span class="badge badge-light-primary rounded-0 w-100 py-2">Worker Data</span>
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table table-xs table-bordered" id="summery">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Laminar</th>
                                <th class="text-right">Block</th>
                                <th class="text-right">Bottle</th>
                                <th class="text-right">Explant</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach ($data['worker'] as $item)
                                    <tr>
                                        <td>{{ $loop->index +=1 }}</td>
                                        <td>{{ $item['tc_workers']['name'] }}</td>
                                        <td>{{ $item['tc_workers']['code'] }}</td>
                                        <td>{{ $item['tc_laminars']['code'] }}</td>
                                        <td class="text-right">{{ $item['block_load'] }}</td>
                                        <td class="text-right">{{ $item['bottle_load'] }}</td>
                                        <td class="text-right">{{ $item['explant_load'] }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-light">
                                    <th colspan="4" class="text-right font-weight-bold py-1">Total:</th>
                                    <th class="text-right py-1">{{ $data['block_total'] }}</th>
                                    <th class="text-right py-1">{{ $data['bottle_total'] }}</th>
                                    <th class="text-right py-1">{{ $data['explant_total'] }}</th>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="step3">
            <div class="row align-items-center mt-3">
                <div class="col">
                    <h5>
                        <span class="badge badge-light-primary rounded-0 w-100 py-2">Stock Usage</span>
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table table-xs table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Medium</th>
                                <th>Bottle</th>
                                <th>Agar</th>
                                <th class="text-right">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['init_stocks'] as $item)
                                <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $item['tc_medium_stock_fast']['date_format'] }}</td>
                                    <td>{{ ($item['tc_medium_stock_fast']['id']==0?"IMPORT":$item['tc_medium_stock_fast']['tc_mediums']['code'])  }}</td>
                                    <td>{{ ($item['tc_medium_stock_fast']['id']==0?"IMPORT":$item['tc_medium_stock_fast']['tc_bottles']['code'])  }}</td>
                                    <td>{{ ($item['tc_medium_stock_fast']['id']==0?"IMPORT":$item['tc_medium_stock_fast']['tc_agars']['code'])  }}</td>
                                    <td class="text-right">{{ $item['stock_usage'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold bg-light">
                                <td colspan="5" class="text-right py-1">Bottle Total:</td>
                                <td class="text-right py-1">{{ $data['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header">
        <h5><i class="feather icon-search mr-2"></i> Data Search</h5>
    </div>
    <div class="card-body">
        <table id="myTable" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
                <tr class="text-center align-middle">
                    <th>Block</th>
                    <th>No. Bottle</th>
                    <th>Worker</th>
                </tr>
            </thead>
            <thead id="header-filter" class="bg-white">
                <tr>
                    <th class="bg-white">Block Number</th>
                    <th class="bg-white">Bottle Number</th>
                    <th class="bg-white">Worker Code</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>
@endsection

