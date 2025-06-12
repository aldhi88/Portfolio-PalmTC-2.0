@extends('layouts.master')
@section('css')
@include('modules.callus_list.include.index_css')
@endsection

@section('js')
@include('modules.callus_list.include.index_js')
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
                <span id="alert-area-print"></span>
                <div class="row">
                    <div class="col">
                        <div class="form-group pb-0 mb-0">
                            <label class="font-weight-bold">From Year:</label>
                            <select name="from_year" class="form-control form-control-sm">
                                @if (count($data['years'])==0)
                                    <option value="0">-- No found data --</option>
                                @else
                                    @foreach ($data["years"] as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group pb-0 mb-0">
                            <label class="font-weight-bold">To Year:</label>
                            <select name="to_year" class="form-control form-control-sm">
                                @if (count($data['years'])==0)
                                    <option value="0">-- No found data --</option>
                                @else
                                    @foreach ($data["years"] as $item)
                                        <option {{ $loop->last?'selected':null }} value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group h-100">
                            {{-- <label>&nbsp;</label> --}}
                            <div class="btn-group btn-group-sm border btn-block h-100">
                                <button style="margin-right: 1px" class="border-right btn btn-light" id="btnPdf"><i class="fas fa-file-pdf mr-2 text-danger"></i> Export to PDF</button>
                                <button class="btn btn-light" id="btnExcel"><i class="fas fa-file-excel mr-2 text-success"></i> Export to XLS</button>
                                <button class="btn btn-primary" id="btnPrint"><i class="feather icon-printer mr-2"></i> Print</button>
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
                    <div class="col"><h5><i class="feather icon-file-text"></i> All Data By Sample</h5></div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th class="text-center">Sampling</th>
                            <th>Sampling Date</th>
                            <th>Total Explant</th>
                            <th>Reacting Explant</th>
                            <th>% Callogenesis</th>
                            <th>58 Flask (nbr)</th>
                            <th>Type</th>
                            <th>Program</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>

    </div>
</div>
@endsection

