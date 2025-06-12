@extends('layouts.master')
@section('css')
    @include('modules.sample.include.index_css')
@endsection

@section('js')
    @include('modules.sample.include.index_js')
    @include('modules.sample.include.delete_js')
    @include('modules.sample.include.import_js')
@endsection

@section('content')

    <div id="exportPrint" class="d-none"></div>
    <span id="alert-area"></span>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5><i class="feather icon-printer"></i> Print Data Sample</h5>
                </div>
                <div class="card-body">
                    <span id="alert-area-print"></span>
                    <div class="row">
                        <div class="col">
                            <div class="form-group pb-0 mb-0 fill">
                                <label class="font-weight-bold">From Year:</label>
                                <select name="from_year" class="form-control form-control-sm">
                                    @if (count($data['years']) == 0)
                                        <option value="0">-- No found data --</option>
                                    @else
                                        @foreach ($data['years'] as $item)
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
                                    @if (count($data['years']) == 0)
                                        <option value="0">-- No found data --</option>
                                    @else
                                        @foreach ($data['years'] as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group h-100">
                                <div class="btn-group btn-group-sm border btn-block h-100">
                                    <button style="margin-right: 1px" class="border-right btn btn-light" id="btnPdf"><i
                                            class="fas fa-file-pdf mr-2 text-danger"></i> Export to PDF</button>
                                    <button class="btn btn-light" id="btnExcel"><i
                                            class="fas fa-file-excel mr-2 text-success"></i> Export to XLS</button>
                                    <button class="btn btn-primary" id="btnPrint"><i class="feather icon-printer mr-2"></i>
                                        Print</button>
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
                        <div class="col">
                            <a href="{{ route('samples.create') }}" class="btn btn-primary btn-sm"><i
                                    class="feather mr-2 icon-plus"></i>Add New</a>
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-horizontal"></i>
                                </button>
                                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-item"><a href="#!" data-toggle="modal"
                                            data-target="#importModal"><span><i class="feather icon-upload"></i> Upload Data
                                                Excel</span></a></li>
                                    <li class="dropdown-item"><a href="{{ route('import.sampleExport') }}"><span><i
                                                    class="feather icon-download"></i> Download Excel Template</span></a>
                                    </li>
                                    {{-- <li class="dropdown-item"><a href="#!" data-toggle="modal" data-target="#importHelp"><span><i class="feather icon-help-circle"></i> Import Guide</span></a></li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="mb-0"><i class="feather icon-file-text"></i> All Data Sample</h5>
                        </div>
                    </div>
                    <hr>
                    <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                        <thead>
                            <tr>
                                <th style="min-width: 100px">Sample Number</th>
                                <th class="text-center">Resampling</th>
                                <th>Year</th>
                                <th>Month</th>
                                <th>Week</th>
                                <th>Cross</th>
                                <th>Family</th>
                                <th>Female Genitor</th>
                                <th>Male Genitor</th>
                                <th>Block</th>
                                <th>Row</th>
                                <th>Palm N°</th>
                                <th>Planting Year</th>
                                <th>Type</th>
                                <th>Program</th>
                            </tr>
                        </thead>
                        <thead id="header-filter" class="bg-white">
                            <tr>
                                <th class="bg-white">No.</th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
                                <th class="bg-white" disable="true"></th>
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
    @include('modules.sample.include.modal')
@endsection
