@extends('layouts.master')
@section('css')
@include('modules.aclim_list.include.show_css')
@endsection

@section('js')
@include('modules.aclim_list.include.show_js')
@endsection

@section('content')
<span id="alert-area"></span>

<div class="row">
    <div class="col">


        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5><i class="feather icon-file-text"></i> All Data Per Date</h5>
                        <a href="{{ route('aclim-lists.index') }}" class="btn btn-warning btn-sm has-ripple"><i class="fas fa-backward mr-2"></i>Back</a>
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
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>Program</th>
                            <th>Sample</th>
                            <th>Worker</th>
                            <th>First<br>Total</th>
                            <th>Total<br>Death</th>
                            <th>Total<br>Transfer</th>
                            <th>Acclim<br>Active</th>
                        </tr>
                    </thead>
                    <thead id="header-filter2" class="bg-white">
                        <tr>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
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
                        <h5><i class="feather icon-file-text"></i> Detail By Selected Data : <span id="select-data">-</span></h5>
                        <input type="hidden" name="tab2Filter" value="0">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Number</th>
                            <th>Status</th>
                            <th width="100">Root Score</th>
                        </tr>

                    </thead>
                    <thead id="header-filter2" class="bg-white">
                        <tr>
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
@endsection




