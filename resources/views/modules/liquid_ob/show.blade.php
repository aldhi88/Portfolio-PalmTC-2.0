@extends('layouts.master')
@section('css')
@include('modules.liquid_ob.include.show_css')
@endsection

@section('js')
@include('modules.liquid_ob.include.show_js')
@endsection

@section('content')
{{-- <div id="exportPrint" class="d-none"></div> --}}

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="col">
                    <a href="{{ route('liquid-obs.index') }}" class="btn btn-warning btn-sm rounded-0"><i class="fas fa-backward mr-2"></i>Back</a>
                </div>
                <div class="col text-right">
                    <h5>Sample:</h5>
                    <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                        <i class="fas fa-eye-dropper mr-2"></i>{{ $data['sampleNumber'] }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col">
                        <div class="row text-center">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Bottle: </strong><br>{{ $data['totalBottle'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Obs Count: </strong><br>{{ $data['obsCount'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Liquid: </strong><br>{{ $data['totalLiquid'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Oxidate: </strong><br>{{ $data['totalOxidate'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Contam: </strong><br>{{ $data['totalContam'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Other: </strong><br>{{ $data['totalOther'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5><i class="feather icon-file-text"></i> Summary Per Observation Date</h5>
                    </div>
                    <div class="col text-right">
                        @if ($data['allowObs'])
                            <a href="{{ route('liquid-obs.create',$data['obId']) }}" class="btn btn-primary btn-sm rounded-0"><i class="feather mr-2 icon-plus"></i>New Observation</a>
                        @endif
                        <a href="{{ route('liquid-transfers.create', $data['initId']) }}" class="btn btn-danger btn-sm rounded-0">
                            <i class="fas fa-share mr-2"></i>Transfer
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <span id="alert-area"></span>
                @if (!$data['allowObs'])
                <div class="alert alert-danger text-center">
                    <p class="m-0 p-0"><i class="fa fa-times fa-fw"></i> Tidak bisa melakukan observasi baru sebelum proses transfer.</p>
                </div>
                @endif
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Obs<br>Date</th>
                            <th>Alpha</th>
                            <th>Cycle</th>
                            <th>Worker</th>
                            <th>Bottle<br>Liquid</th>
                            <th>Bottle<br>Oxidate</th>
                            <th>Bottle<br>Contam</th>
                            <th>Bottle<br>Other</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5><i class="feather icon-file-text"></i> Summary Per Bottle Date</h5>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th rowspan="2">Program</th>
                            <th rowspan="2">Sample</th>
                            <th rowspan="2">Bottle<br>Date</th>
                            <th rowspan="2">Alpha</th>
                            <th rowspan="2">Cycle</th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">First<br>Total</th>
                            <th class="text-center" colspan="6">Observation</th>
                            <th rowspan="2">Last<br>Total</th>
                        </tr>
                        <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Worker</th>
                            <th class="text-center bg-success text-light">Liq</th>
                            <th class="text-center bg-danger text-light">Oxi</th>
                            <th class="text-center bg-danger text-light">Con</th>
                            <th class="text-center bg-danger text-light">Oth</th>
                        </tr>
                    </thead>
                    <thead id="header-filter2" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
</div>
@include('modules.liquid_ob.include.modal')
@endsection




