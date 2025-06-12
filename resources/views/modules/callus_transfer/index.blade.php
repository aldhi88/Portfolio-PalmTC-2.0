@extends('layouts.master')

{{-- @if ($data['dtObs']) --}}



@section('css')
@include('modules.callus_transfer.include.index_css')
@endsection

@section('js')
@include('modules.callus_transfer.include.index_js')
@endsection

@section('content')

<div class="row">
    <div class="col">
        <span id="alert-area-sample"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col"><h5 class="mt-2"><i class="feather icon-file-text"></i> Transfer Summary</h5></div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th rowspan="0">Sample</th>
                            <th rowspan="0">Initiation<br>Date</th>
                            <th rowspan="0">Total<br>Observation</th>
                            <th rowspan="0">Total Bottle<br>Grow Callus</th>
                            <th rowspan="0">Transferred</th>
                            <th rowspan="0">Transfer<br>Bottle Left</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white">Ex: 12</th>
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
@endsection

{{-- @else
@section('content')
    <div class="alert alert-danger" role="alert">
        Observation data is not found, please click the following <a href="{{ route('callus-obs.index') }}" class="alert-link">link</a> to create observation data.
    </div>
@endsection
@endif --}}
