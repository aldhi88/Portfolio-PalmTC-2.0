@extends('layouts.master')

@section('css')
    @include('modules.migration.include.index_css')
@endsection

@section('js')
    @include('modules.migration.include.index_js')
@endsection

@section('content')
<div class="row">
    <div class="col">
        <span id="alert-area"></span>
        <div class="card">
            <!-- <div class="card-header">
                <h5>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal"><i class="feather mr-2 icon-plus"></i>Add New</button>
                </h5>
                @include('components.card_tool')
            </div> -->
            <div class="card-body">
                <table id="myTable" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Migration</th>
                            <th width="10">Batch</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('modules.migration.include.modal')
@endsection



