@extends('layouts.master')

@section('css')
    @include('modules.contamination.include.index_css')
@endsection

@section('js')
    @include('modules.contamination.include.index_js')
    @include('modules.contamination.include.create_js')
    @include('modules.contamination.include.edit_js')
    @include('modules.contamination.include.delete_js')
@endsection


@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        <h5>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal"><i class="feather mr-2 icon-plus"></i>Add New</button>
        </h5>

        @include('components.card_tool')
    </div>
    <div class="card-body">
        <table id="myTable" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
                <tr>
                    <th>Contamination Name</th>
                    <th>Contamination Code</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@include('modules.contamination.include.modal')

@endsection

