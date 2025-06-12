@extends('layouts.master')

{{-- @if ($data['dtInit']) --}}

@section('css')
@include('modules.callus_ob.include.index_css')
@endsection

@section('js')
@include('modules.callus_ob.include.index_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div id="exportPrint" class="d-none"></div>
<div class="row">
    <div class="col">
        <span id="alert-area-sample"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col"><h5 class="mt-2"><i class="feather icon-file-text"></i> Observation Summary</h5></div>
                    <div class="col-md-3 text-right">
                        <label class="font-weight-bold mt-2">Page Number (25 row/page):</label>
                    </div>
                    <div class="col text-right">
                        <div class="input-group">
                            <input type="number" min="1" required value="1" name="page" class="form-control form-control-sm" placeholder="Number of row">
                            <button id="btnPrint" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Print Blank Form</button>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-horizontal"></i>
                                </button>
                                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-item"><a href="#!" data-toggle="modal" data-target="#importModal"><span><i class="feather icon-upload"></i> Upload Data Excel</span></a></li>
                                    <li class="dropdown-item"><a href="{{route('import.callusExport')}}"><span><i class="feather icon-download"></i> Download Excel Template</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            {{-- <th rowspan="2"><i class="fas fa-bell"></i></th> --}}
                            <th rowspan="2">Sample</th>
                            <th rowspan="2" width="70">Initiation<br>Date</th>
                            <th rowspan="2" width="40">Total Obs</th>
                            <th colspan="2" class="text-center">Grow Callus</th>
                            <th colspan="2" class="text-center">Callus ( <i class="fas fa-percent"></i> )</th>
                            <th colspan="2" class="text-center">Oxidate</th>
                            <th colspan="2" class="text-center">Contamination</th>
                        </tr>
                        <tr>
                            {{-- Grow Callus --}}
                            <th class="text-center"><i class="fas fa-flask text-primary"></i></th>
                            <th class="text-center"><i class="fas fa-leaf text-success"></i></th>
                            {{-- Callus Percentage (%) --}}
                            <th class="text-center"><i class="fas fa-flask text-primary"></i></th>
                            <th class="text-center"><i class="fas fa-leaf text-success"></i></th>
                            {{-- Contamination --}}
                            <th class="text-center"><i class="fas fa-flask text-warning"></i></th>
                            <th class="text-center"><i class="fas fa-leaf text-warning"></i></th>
                            {{-- Oxidate --}}
                            <th class="text-center"><i class="fas fa-flask text-danger"></i></th>
                            <th class="text-center"><i class="fas fa-leaf text-danger"></i></th>

                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            {{-- <th class="bg-white" disable="true"></th> --}}
                            <th class="bg-white">Ex: 12</th>
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

<div id="importModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Import Data From Excel File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formImportModal">@csrf
                <div class="modal-body">
                    {{-- <div class="alert alert-danger" role="alert">
                        Pada proses ini, setiap baris data di excel akan dibuatkan 480 baris data botol, maka proses import akan sangat lama. Untuk 1 baris data excel menghabiskan waktu 20 detik.
                    </div> --}}
                    <div class="form-group">
                        <label><strong>Choose File</strong></label>
                        <input name="file" type="file" class="form-control form-control-sm">
                        <small><span class="file text-danger msg"></span></small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- @else
@section('content')
    <div class="alert alert-danger" role="alert">
        Initiation data is not found, please click the following <a href="{{ route('inits.create') }}" class="alert-link">link</a> to create initiation data.
    </div>
@endsection
@endif --}}
