@extends('layouts.master')

@section('css')
    @include('modules.callus_ob.include.comment_css')
@endsection

@section('js')
    @include('modules.callus_ob.include.comment_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        <a href="{{ route('field-lists.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to Data</a>
        <a href="{{ route('field-lists.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to Data</a>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h5>Comments - Files - Images</h5>
                    </div>
                    <div class="col text-right">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCommentModal">Add New</button>
                    </div>
                </div>
                <table id="DTComment" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>Comment</th>
                            <th>File List</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="text-center">
                        <th class="bg-white" disable="true"></th>
                        <th class="bg-white" disable="true"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white" disable="true"></th>
                        <th class="bg-white" disable="true"></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>

</div>

<div id="treefileModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content"></div>
    </div>
</div>

@include('modules.callus_ob.comment_create',['data' => $data])
@include('modules.callus_ob.comment_delete',['data' => $data])
@endsection

