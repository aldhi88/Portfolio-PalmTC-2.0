@extends('layouts.master')
@section('css')
@include('modules.aclim_ob.include.index_css')
@endsection

@section('js')
@include('modules.aclim_ob.include.index_js')
@endsection

@section('content')
<div id="exportPrint" class="d-none"></div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col"><h5><i class="feather icon-file-text"></i> All Data Bottle</h5></div>
                    {{-- <div class="col text-right">
                        <button id="btnPrint" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Print Form</button>
                    </div> --}}
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th rowspan="2">Sample</th>
                            <th rowspan="2">Program</th>
                            <th rowspan="2">Date<br>Active</th>
                            <th rowspan="2">Obs<br>Count</th>
                            <th rowspan="2">First<br>Total</th>
                            <th colspan="{{ count($data['death'])+1 }}" class="text-center bg-danger text-white">Observation</th>
                            <th rowspan="2" class="bg-success text-white">Transfer</th>
                            <th rowspan="2">Total<br>Active</th>
                        </tr>
                        <tr>

                            @foreach ($data['death'] as $item)
                                <th class="bg-warning">{{ $item['code'] }}</th>
                            @endforeach
                            <th class="bg-danger text-white">Total</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            @foreach ($data['death'] as $item)
                            <th class="bg-white" disable="true"></th>
                            @endforeach
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
{{-- @include('modules.embryo_list.include.modal') --}}
@endsection




