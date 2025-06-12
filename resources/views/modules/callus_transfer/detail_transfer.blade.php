@extends('layouts.master')
@section('css')
@include('modules.callus_transfer.include.detail_transfer_css')
@endsection

@section('js')
@include('modules.callus_transfer.include.detail_transfer_js')
@endsection

@section('content')
{{-- print element --}}
<div id="exportPrint" class="d-none"></div>

<div class="row">
    <div class="col">
        <div class="card border border-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a href="{{ route('callus-transfers.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Transfer Summary</a>
                    </div>
                    <div class="col text-right" style="top: 5px">
                        <h5>Sample : </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i>{{ $data['sampleNumber'] }}
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
                                    <label><strong>Total Observation: </strong></label>
                                    <span>{{ $data['totalObs'] }} <sub>x</sub> </span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Bottle Grow Callus: </strong></label>
                                    <span>{{ $data['totalBottleCallus'] }} <sub>bottle</sub></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Transferred: </strong></label>
                                    <span>{{ $data['bottleTransfered'] }} <sub>bottle</sub> </span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Bottle Left: </strong></label>
                                    <span>{{ $data['bottleLeft'] }} <sub>bottle</sub> </span>
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
        <span id="alert-area-sample"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> Observation Summary</h5></div>
                    <div class="col-md-3 text-right">
                        <label class="font-weight-bold mt-2">Page Number (25 row/page):</label>
                    </div>
                    <div class="col text-right">
                        <div class="input-group">
                            <input type="number" min="1" required value="1" name="page" class="form-control form-control-sm" placeholder="Number of row">
                            <button id="btnPrint" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Print Blank Form</button>
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
                                    <th>No</th>
                                    <th>Observation <br> Date</th>
                                    <th>Total Bottle <br> Grow Callus</th>
                                    <th>Transferred</th>
                                    <th>Bottle Left</th>
                                    <th></th>
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

<div class="row">
    <div class="col">
        <span id="alert-area-sample"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> All Transfer Data</h5></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                                <thead>
                                    <tr>
                                        <th>Transfer <br> Date</th>
                                        <th>Observation <br> Date</th>
                                        <th>Sub<br>Culture</th>
                                        <th>Worker</th>
                                        <th>Laminar</th>
                                        <th>Worked <br> Bottle</th>
                                        <th>New<br>Bottle</th>
                                        <th>Time<br>Work</th>
                                    </tr>
                                </thead>
                                <thead id="header-filter" class="bg-white">
                                    <tr>
                                        <th class="bg-white" disable="true"></th>
                                        <th class="bg-white"></th>
                                        <th class="bg-white" disable="true"></th>
                                        <th class="bg-white">....</th>
                                        <th class="bg-white">....</th>
                                        <th class="bg-white">....</th>
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
            
        </div>

    </div>
</div>

{{-- @include('modules.callus_observation.include.modal') --}}
@endsection

