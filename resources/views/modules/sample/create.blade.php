@extends('layouts.master')

@section('css')
    @include('modules.sample.include.index_css')
@endsection

@section('js')
    @include('modules.sample.include.create_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="card">
    <div class="card-header">
        <a href="{{ route('samples.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to Sample Data</a>
        <a href="{{ route('samples.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to Sample Data</a>
    </div>

    <form id="formCreateModal"> @csrf
        <div class="card-body">
            <div class="row">

                <div class="col-md-7">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Sample Number</strong></label>
                                <input type="text" name="display_number" readonly disabled class="form-control form-control-sm px-1 font-weight-bold">
                                <input type="hidden" name="sample_number">
                                <small><span class="msg text-danger sample_number"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label><strong>Date</strong></label>
                                        <input type="date" value="{{ date('Y-m-d') }}" name="created_at" class="form-control form-control-sm">
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
                            <label><strong>Selection Number</strong></label>
                            <div class="input-group mb-3">
                                <input type="text" placeholder="Please select your data" readonly name="no_seleksi" class="form-control form-control-sm px-1">
                                <small><span class="text-danger no_seleksi msg"></span></small>
                                <input type="hidden" name="master_treefile_id">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary btn-sm btn-block" data-toggle="modal" data-target="#treefileModal"><i class="feather mr-1 icon-search"></i>Select</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Program</strong></label>
                                <input type="text" name="program" class="form-control form-control-sm" style="text-transform:uppercase">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col">
                    <div class="form-group">
                        <label><strong>Note/Desc</strong></label>
                        <textarea name="desc" class="form-control form-control-sm" rows="8"></textarea>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn float-right btn-sm btn-primary"><i class="feather mr-2 icon-save"></i>Save New Sample</button>
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

@endsection

