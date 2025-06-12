@extends('layouts.master')
@section('css')
@include('modules.embryo_list.include.index_css')
@endsection

@section('js')
@include('modules.embryo_list.include.index_js')
@endsection

@section('content')
<span id="alert-area"></span>

<div class="row">
    <div class="col">

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col"><h5><i class="feather icon-file-text"></i> All Data Bottle</h5></div>
                </div>
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-horizontal"></i>
                        </button>
                        <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item"><a href="#!" data-toggle="modal" data-target="#importModal"><span><i class="feather icon-upload"></i> Upload Data Excel</span></a></li>
                            <li class="dropdown-item"><a href="{{ route('import.embryoExport') }}"><span><i class="feather icon-download"></i> Download Excel Template</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Sample</th>
                            <th>Program</th>
                            <th>Total Bottle</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
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

<div id="importModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Import Data From Excel File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formImportModal">@csrf
                <div class="modal-body">
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




