@extends('layouts.master')
@section('css')
@include('modules.nur_transfer.include.show_css')
@endsection

@section('js')
@include('modules.nur_transfer.include.show_js')
@include('modules.nur_transfer.include.delete_js')
@include('modules.nur_transfer.include.transfer_js')
@endsection

@section('content')
{{-- print element --}}
<div id="exportPrint" class="d-none"></div>

<div class="row">
    <div class="col">
        <div class="card border border-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a href="{{ route('nur-transfers.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Transfer Summary</a>
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
                <div class="row">
                    <div class="col">
                        <div class="row text-center">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Transfer Count: </strong></label><br>{{ $data['transferCount'] }}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Transferred: </strong></label><br>{{ $data['transferred'] }}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Need Transfer: </strong></label><br>{{ $data['need_transfer'] }}
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
        <span id="alert-area"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> All Transfer Pending</h5></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th>Harden<br>Date</th>
                                    <th>Obs<br>Date</th>
                                    <th>Worker</th>
                                    <th>Need Transfer</th>
                                    <th>Category</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</div>

<div class="row">
    <div class="col">
        <span id="alert-area2"></span>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> All Successful Transfer</h5></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th>Transfer<br>Date</th>
                                    <th>Obs<br>Date</th>
                                    <th>Worker</th>
                                    <th>To<br>Nursery</th>
                                    <th>To<br>Estate</th>
                                    <th>To<br>Field</th>
                                    <th width="300">#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</div>

@include('modules.nur_transfer.include.modal')


@endsection

