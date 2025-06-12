@extends('layouts.master')
@section('css')
@include('modules.embryo_transfer.include.index_css')
@endsection
@section('js')
@include('modules.embryo_transfer.include.index_js')
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
                            <th>Sample</th>
                            <th>Program</th>
                            <th>Transfer<br>Count</th>
                            <th>Bottle<br>Embryo</th>
                            <th>Transferred</th>
                            <th>Need<br>Transfer</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white">Ex: 12</th>
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

@endsection
