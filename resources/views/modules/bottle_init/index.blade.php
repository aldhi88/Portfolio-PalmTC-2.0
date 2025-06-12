@extends('layouts.master')

@section('css')
    @include('modules.bottle_init.include.index_css')
@endsection

@section('js')
    @include('modules.bottle_init.include.index_js')
    @include('modules.bottle_init.include.create_js')
    @include('modules.bottle_init.include.edit_js')
    @include('modules.bottle_init.include.bottle_list_js')
@endsection

@section('content')
<div class="row">
    <div class="col">
        <span id="alert-area"></span>
        <div class="card">
            <div class="card-header">
                <h5>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal"><i class="feather mr-2 icon-plus"></i>Add New Column</button>
                </h5>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Column Name</th>
                            <th>Bottle List</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('modules.bottle_init.include.modal')
@endsection

