@extends('layouts.master')

@section('css')
    @include('modules.opname.include.index_css')
@endsection

@section('js')
    @include('modules.opname.include.modal')
    @include('modules.opname.include.index_js')
    @include('modules.opname.include.create_js')
    @include('modules.opname.include.edit_js')
    @include('modules.opname.include.delete_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        {{-- <h5>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal"><i class="feather mr-2 icon-plus"></i>Add New</button>
        </h5> --}}
        <div class="card-header-right">
            <div class="btn-group card-option">
                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="feather icon-more-horizontal"></i>
                </button>
                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                    <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                    <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                    <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                    <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{-- <div class="dt-responsive table-responsive"> --}}
            <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            {{-- <table id="simpletable" class="table table-striped table-bordered nowrap"> --}}
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created On</th>
                        <th>Worker</th>
                        <th>Stock In</th>
                        <th>Stock Out</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        {{-- </div> --}}
    </div>
</div>



@endsection

