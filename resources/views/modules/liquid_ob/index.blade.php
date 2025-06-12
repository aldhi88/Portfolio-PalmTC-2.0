@extends('layouts.master')
@section('css')
@include('modules.liquid_ob.include.index_css')
@endsection

@section('js')
@include('modules.liquid_ob.include.index_js')
@endsection

@section('content')
<div id="exportPrint" class="d-none"></div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5><i class="feather icon-printer"></i> Print Data</h5>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col">
                        <div class="row text-center">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Sample: </strong>{{ $data['totalSample'] }}</label>
                                    <span></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Bottle: </strong>{{ $data['totalBottle'] }}</label>
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
                    <div class="col"><h5><i class="feather icon-file-text"></i> All Data Bottle</h5></div>
                    <div class="col text-right">
                        <button id="btnPrint" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Print Form</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Sample</th>
                            <th>Total<br>Bottle</th>
                            <th>Obs<br>Count</th>
                            <th>Total<br>Liquid</th>
                            <th>Total<br>Oxidate</th>
                            <th>Total<br>Contam</th>
                            <th>Total<br>Other</th>
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
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
</div>
{{-- @include('modules.embryo_list.include.modal') --}}
@endsection




