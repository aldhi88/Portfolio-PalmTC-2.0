@extends('layouts.master')

@section('css')
    @include('modules.sample.include.index_css')
@endsection

@section('js')
    @include('modules.sample.include.edit_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        <a href="{{ route('samples.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to Sample Data</a>
        <a href="{{ route('samples.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to Sample Data</a>
    </div>
    <form id="formEditModal"> @csrf @method('PUT')
        <input type="hidden" name="id" value="{{ $data['data_edit']->id }}">
        <div class="card-body">
            <div class="row">

                <div class="col-md-7">
                    <div class="row">

                        <div class="col">
                            <div class="form-group">
                                <label><strong>Sample Number</strong></label>
                                <input type="text" value="{{ $data['data_edit']->sample_number_display }}" name="display_number" readonly disabled class="form-control form-control-sm px-1 font-weight-bold">
                                <input type="hidden" name="sample_number" value="{{ $data['data_edit']->sample_number }}">
                                <small><span class="msg text-danger sample_number"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label><strong>Date</strong></label>
                                        <input type="date" value="{{ $data['data_edit']->created_at_edit }}" name="created_at" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label><strong>Week</strong></label>
                                        <input type="text" disabled readonly name="week" class="form-control form-control-sm px-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col">
                            <div class="form-group">
                                <label><strong>Selection Number</strong></label>
                                <div class="input-group mb-3">
                                    <input type="text" value="{{ $data['data_edit']->master_treefile->noseleksi }}" placeholder="Please select your data" readonly name="no_seleksi" class="form-control form-control-sm px-1">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary btn-sm btn-block" data-toggle="modal" data-target="#treefileModal"><i class="feather mr-1 icon-search"></i>Select</button>
                                    </div>
                                </div>
                                <small><span class="text-danger no_seleksi msg"></span></small>
                                <input type="hidden" name="master_treefile_id" value="{{ $data['data_edit']->master_treefile_id }}">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Program</strong></label>
                                <input type="text" name="program" value="{{ $data['data_edit']->program }}" class="form-control form-control-sm" style="text-transform:uppercase">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Note/Desc</strong></label>
                        <textarea name="desc" class="form-control form-control-sm" rows="8">{{ $data['data_edit']->desc }}</textarea>
                    </div>
                </div>



                <div class="col-12 mt-5">
                    <div class="row">
                        <div class="col">
                            <h5>Comments - Files - Images</h5>
                        </div>
                        <div class="col text-right">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCommentModal" data-id="{{$data['data_edit']->id}}">Add New</button>
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
        <div class="card-footer">
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn float-right btn-sm btn-primary"><i class="feather mr-2 icon-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

    </form>
</div>

<div id="treefileModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content"></div>
    </div>
</div>

@include('modules.sample.comment_create',['data' => $data])
@include('modules.sample.comment_delete',['data' => $data])
@endsection

