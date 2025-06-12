@extends('layouts.master')
@section('css')
@include('modules.rooting_ob.include.show_css')
@endsection

@section('js')
@include('modules.rooting_ob.include.show_js')
@endsection

@section('content')
{{-- <div id="exportPrint" class="d-none"></div> --}}

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="col">
                    <h5><i class="fas fa-align-justify"></i> Summary</h5>
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
                                    <label><strong>Total Rooting: </strong><br>{{ $data['totalRooting'] }}</label>
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
                            <a href="{{ route('rooting-obs.create',$data['obId']) }}" class="btn btn-primary btn-sm rounded-0"><i class="feather mr-2 icon-plus"></i>New Observation</a>
                        @endif
                        <a href="{{ route('rooting-transfers.create', $data['initId']) }}" class="btn btn-danger btn-sm rounded-0">
                            <i class="fas fa-share mr-2"></i>Transfer
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <span id="alert-area2"></span>
                @if (!$data['allowObs'])
                <div class="alert alert-danger text-center">
                    <p class="m-0 p-0"><i class="fa fa-times fa-fw"></i> Tidak bisa melakukan observasi baru sebelum proses transfer.</p>
                </div>
                @endif
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th rowspan="2">Obs<br>Date</th>
                            <th rowspan="2">Alpha</th>
                            <th rowspan="2">Worker</th>
                            <th colspan="2" class="text-center">Rooting</th>
                            <th colspan="2" class="text-center">Oxidate</th>
                            <th colspan="2" class="text-center">Contam</th>
                            <th colspan="2" class="text-center">Other</th>
                        </tr>
                        <tr>
                            <th>Bottle</th>
                            <th>Plantlet</th>
                            <th>Bottle</th>
                            <th>Plantlet</th>
                            <th>Bottle</th>
                            <th>Plantlet</th>
                            <th>Bottle</th>
                            <th>Plantlet</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
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
                            <th rowspan="2">Name</th>
                            <th rowspan="2">First<br>Total</th>
                            <th class="text-center" colspan="6">Observation</th>
                            <th rowspan="2">Last<br>Total</th>
                        </tr>
                        <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Worker</th>
                            <th class="text-center bg-success text-light">Root</th>
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
@include('modules.rooting_ob.include.modal')
@endsection




