@extends('layouts.master')
@section('css')
@include('modules.callus_ob.include.detail_observation_css')
@endsection

@section('js')
@include('modules.callus_ob.include.detail_observation_js')
@endsection

@section('content')
{{-- print element --}}
<div id="exportPrint" class="d-none"></div>

<div class="row">
    <div class="col">
        <div class="card border border-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-2">
                        <a href="{{ route('callus-obs.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Back</a>
                    </div>
                    <div class="col text-center border-left" style="top: 5px">
                        <h5>Sample : </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i>{{ $data['sampleNumber'] }}
                        </span>
                    </div>
                    <div class="col text-center border-left" style="top: 5px">
                        <h5>Last: </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fas fa-calendar-alt mr-2"></i>{{ $data['lastDateOb'] }}
                        </span>
                    </div>
                    <div class="col text-center border-left" style="top: 5px">
                        <h5>Next: </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fas fa-calendar-alt mr-2"></i>{{ $data['nextDateOb'] }}
                        </span>
                    </div>
                    <div class="col text-center border-left" style="top: 5px">
                        <h5>Total: </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="feather icon-check-circle mr-2"></i>{{ $data['countObDone'] }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="row text-center">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Block: </strong></label>
                                    <span>{{ $data['totalBlock'] }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Bottle: </strong></label>
                                    <span>{{ $data['totalBottle'] }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Explant: </strong></label>
                                    <span>{{ $data['totalExplant'] }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Worker: </strong></label>
                                    <span>{{ $data['totalWorker'] }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Room: </strong></label>
                                    <span>{{ $data['initRoom'] }}</span>
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

    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body border border-primary">
                <h6 class="m-b-0">Grow Callus</h6>
                <div class="row">
                    <div class="col">
                        <h4 class="m-t-15 m-b-15"><i class="icon fas fa-flask text-primary mr-2"></i>{{ $data['totalBottleCallusPerInit'] }} <sup class="text-primary">({{ $data['persenBottleCallus'] }}%)</sup></h4>
                        <a href="#" data-toggle="modal" data-target="#totalBottleCallusModal"><p class="m-b-0"><u>Detail</u></p></a>
                    </div>
                    <div class="col">
                        <h4 class="m-t-15 m-b-15"><i class="icon fas fa-leaf text-success mr-2"></i>{{ $data['totalExplantCallusPerInit'] }} <sup class="text-success">({{ $data['persenExplantCallus'] }}%)</sup></h4>
                        <a href="#" data-toggle="modal" data-target="#totalExplantCallusModal"><p class="m-b-0"><u>Detail</u></p></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center">
            <div class="card-body border border-warning">
                <h6 class="m-b-0">Oxidate</h6>
                <div class="row">
                    <div class="col">
                        <h4 class="m-t-15 m-b-15"><i class="icon fas fa-flask text-warning mr-2"></i>{{ $data['totalBottleOxiPerInit'] }}</h4>
                        <a href="#" data-toggle="modal" data-target="#totalBottleOxidateModal"><p class="m-b-0"><u>Detail</u></p></a>
                    </div>
                    <div class="col">
                        <h4 class="m-t-15 m-b-15"><i class="icon fas fa-leaf text-warning mr-2"></i>{{ $data['totalExplantOxiPerInit'] }}</h4>
                        <a href="#" data-toggle="modal" data-target="#totalExplantOxidateModal"><p class="m-b-0"><u>Detail</u></p></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center">
            <div class="card-body border border-danger">
                <h6 class="m-b-0">Contamination</h6>
                <div class="row">
                    <div class="col">
                        <h4 class="m-t-15 m-b-15"><i class="icon fas fa-flask text-danger mr-2"></i>{{ $data['totalBottleContamPerInit'] }}</h4>
                        <a href="#" data-toggle="modal" data-target="#totalBottleContaminateModal"><p class="m-b-0"><u>Detail</u></p></a>
                    </div>
                    <div class="col">
                        <h4 class="m-t-15 m-b-15"><i class="icon fas fa-leaf text-danger mr-2"></i>{{ $data['totalExplantContamPerInit'] }}</h4>
                        <a href="#" data-toggle="modal" data-target="#totalExplantContaminateModal"><p class="m-b-0"><u>Detail</u></p></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="row">
    <div class="col">
        <span id="alert-area-sample"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> All Observation Data</h5></div>
                    <div class="col-md-3 text-right">
                        <label class="font-weight-bold mt-2">Page Number (25 row/page):</label>
                    </div>
                    <div class="col text-right">
                        <div class="input-group">
                            <input type="number" min="1" required value="1" name="page" class="form-control form-control-sm" placeholder="Number of row">
                            <button id="btnPrint" init-id="{{ $data['initId'] }}" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Print Form</button>
                            <a href="{{ route('callus-obs.create',$data['nextObId']) }}" class="btn btn-primary btn-sm rounded-0"><i class="feather mr-2 icon-plus"></i>New Observation</a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th rowspan="2">Action</th>
                                    <th rowspan="2">Observation Date</th>
                                    <th rowspan="2">Worker</th>
                                    <th colspan="4" class="text-center">Grow Callus</th>
                                    <th colspan="2" class="text-center">Oxidate</th>
                                    <th colspan="2" class="text-center">Contamination</th>
                                </tr>
                                <tr>
                                    {{-- Grow Callus --}}
                                    <th width="75"><i class="fas fa-flask text-primary"></i></th>
                                    <th width="75"><i class="fas fa-flask text-secondary"></i></th>
                                    <th width="75"><i class="fas fa-leaf text-success"></i></th>
                                    <th width="75"><i class="fas fa-leaf text-secondary"></i></th>
                                    {{-- Contamination --}}
                                    <th width="75"><i class="fas fa-flask text-warning"></i></th>
                                    <th width="75"><i class="fas fa-leaf text-warning"></i></th>
                                    {{-- Oxidate --}}
                                    <th width="75"><i class="fas fa-flask text-danger"></i></th>
                                    <th width="75"><i class="fas fa-leaf text-danger"></i></th>
                                </tr>
                            </thead>
                            
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</div>

@include('modules.callus_ob.include.modal')
@endsection

