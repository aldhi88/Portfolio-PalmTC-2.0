@extends('layouts.master')
@section('css')
@include('modules.embryo_ob.include.view_bottle_css')
@endsection

@section('js')
@include('modules.embryo_ob.include.view_bottle_js')
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-backward mr-2"></i>
                            Back to Summary
                        </a>
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
                    <div class="col"><h5><i class="feather icon-file-text"></i> List Embryogenesis Bottles</h5></div>
                    <div class="col text-right">
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Sample: </strong></label>
                                    <span>{{ $data['init']->tc_samples->sample_number_display }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Total Bottle: </strong></label>
                                    <span>{{ $data['init']->total_bottle }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>From <br> Transfer Date</th>
                            <th>Sample</th>
                            <th>Worker</th>
                            <th>Sub Culture</th>
                            <th>Sub Culture</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white">....</th>
                            <th class="bg-white">....</th>
                            <th class="bg-white">....</th>
                            <th class="bg-white">....</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>

    </div>
</div>
@endsection

