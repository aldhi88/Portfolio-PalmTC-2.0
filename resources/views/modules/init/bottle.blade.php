@extends('layouts.master')

@section('css')
    @include('modules.init.include.bottle_css')
@endsection

@section('js')
    @include('modules.init.include.bottle_js')
@endsection

@section('content')
<input type="hidden" name="initId" value="{{ $data['tc_init_id'] }}">
<div class="row">
    <div class="col">
        <span id="alert-area"></span>
        <div class="card">
            <div class="card-header">
                <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
                <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col">
                        <h5>Bottle Detail Summary</h5>
                        <div class="table-container"></div>
                    </div>
                </div>
                <hr>

                <h5>Add New Bottle</h5><hr>
                <span id="alert-area-addBottle"></span>
                <form id="formAddBottleWorker">@csrf
                    <input type="hidden" name="tc_init_id" value="{{ $data["tc_init_id"] }}">
                    <input type="hidden" name="tc_laminar_id" value="{{ $data["tc_init_id"] }}">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Worker</strong></label>
                                <select name="tc_worker_id" class="form-control form-control-sm">
                                    @foreach ($data["worker"] as $item)
                                        <option value="{{ $item[0]['tc_worker_id'] }}">{{ $item[0]['tc_workers']['code'] }}</option>
                                    @endforeach
                                </select>
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Block</strong></label>
                                <span id="no_block_area"></span>
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Medium Stock</strong></label>
                                <select name="tc_medium_stock_id" class="form-control form-control-sm">
                                    @foreach ($data["stocks"] as $item)
                                        <option value="{{ $item['tc_medium_stock_id'] }}">{{ $item['tc_medium_stock_fast']['date_short'] }}</option>
                                    @endforeach
                                </select>
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Added Bottle</strong></label>
                                <input name="number_of_bottle" type="number" value="1" min="1" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong></strong></label>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">Add New Bottle</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>
                <h5>Disable Spesific Bottles</h5><hr>
                <div class="row">
                    <div class="col">
                        <div class="dt-responsive table-responsive">

                            <table id="myTable" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr class="text-center align-middle">
                                        <th>Block</th>
                                        <th>Bottle</th>
                                        <th>Worker</th>
                                        <th>Disable</th>
                                    </tr>
                                </thead>
                                <thead id="header-filter" class="bg-white">
                                    <tr>
                                        <th class="bg-white"></th>
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
        </div>
    </div>
</div>
@endsection

