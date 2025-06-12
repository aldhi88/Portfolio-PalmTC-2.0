@extends('layouts.master')
@section('css')
@include('modules.embryo_list.include.show_css')
@endsection

@section('js')
@include('modules.embryo_list.include.show_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="row">
    <div class="col">

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5><i class="feather icon-file-text"></i> All Data Bottle</h5>
                        <a href="{{ route('embryo-lists.index') }}" class="btn btn-warning btn-sm has-ripple"><i class="fas fa-backward mr-2"></i>Back</a>
                        <a href="{{ route('embryo-obs.show',$data['initId']) }}" class="btn btn-primary btn-sm has-ripple"><i class="fas fa-tasks mr-2"></i>Observation</a>
                    </div>
                    <div class="col text-right">
                        <h5>Sample:</h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i>{{ $data['sampleNumber'] }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr class="text-center">
                            <th></th>
                            <th>Bottle<br>Date</th>
                            <th>Program</th>
                            <th>Sample</th>
                            <th>Sub<br>Culture</th>
                            <th>Worker</th>
                            <th>First<br>Total</th>
                            <th>Last<br>Total</th>
                        </tr>
                    </thead>

                    <thead>
                        <tr class="bg-danger">
                            <th colspan="6" class="text-right bg-success text-white">Total: </th>
                            <th id="firstTotal" class="text-center"></th>
                            <th id="lastTotal" class="text-center"></th>
                        </tr>
                    </thead>

                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
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
                        <h5><i class="feather icon-file-text"></i> Summary With History</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Bottle<br>Date</th>
                            <th>Program</th>
                            <th>Sample</th>
                            <th>Sub</th>
                            <th>Name</th>
                            <th>First<br>Total</th>
                            <th>Obs<br>Date</th>
                            <th>Transfer<br>Date</th>
                            <th>Worker</th>
                            <th>Last<br>Total</th>
                        </tr>

                    </thead>
                    <thead id="header-filter2" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
</div>
@include('modules.embryo_list.include.modal')
@endsection




