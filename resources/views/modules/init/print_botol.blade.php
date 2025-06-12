@extends('layouts.master')

@section('css')
    @include('modules.init.include.print_bottle_css')
@endsection

@section('js')
    @include('modules.init.include.print_bottle_js')
@endsection

@section('content')

<div class="row">
    <div class="col">
        <span id="alert-area"></span>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
                        <a href="{{ route('inits.index') }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to All Initiation Data</a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <span id="alert-area-b"></span>
                <div class="row">
                    <div class="col">
                        <div class="bg-light border px-3 pb-0 pt-2">
                            
                            <div class="row">
                                <div class="col text-center">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group mb-1 border-right border-bottom">
                                                <label><strong>Sampling: </strong></label>
                                                <span>{{ $data['initiations']->tc_samples->sample_number_display }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-right border-bottom">
                                                <label><strong>Initiation Date: </strong></label>
                                                <span>{{ $data['initiations']->created_at_long_format }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-bottom">
                                                <label><strong>Working Date: </strong></label>
                                                <span>{{ $data['initiations']->date_work_format }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col text-center">
                                            <div class="form-group mb-0 border-right">
                                                <label><strong>Block: </strong></label>
                                                <span>{{ $data['initiations']->number_of_block }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-0 border-right">
                                                <label><strong>Bottle/Block: </strong></label>
                                                <span>{{ $data['initiations']->number_of_bottle }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-0 border-right">
                                                <label><strong>Plant/Bottle: </strong></label>
                                                <span>{{ $data['initiations']->number_of_plant }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-0">
                                                <label><strong>Room: </strong></label>
                                                <span>{{ $data['initiations']->tc_rooms->code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                
                            </div>
                                        
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<span id="alert-area-a"></span>
<input type="hidden" value="{{ $data['tc_init_id'] }}" name="tc_init_id">
<div class="row">
    <div class="col">
        <form id="printByBottleNumber">
            <div class="card">
                <div class="card-body text-center pb-0">
                    <i class="fas fa-prescription-bottle text-c-blue d-block f-30"></i>
                    <h5 class="m-t-20"><span class="text-c-blue"></span><i class="fas fa-qrcode text-dark mr-2"></i>Print By Bottle Number</h5>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>From Number:</label>
                                <input name="from_number" type="number" value="1" min="1" max="{{ $data["max_bottle_number"] }}" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>To Number:</label>
                                <input name="to_number" type="number" value="{{ $data["max_bottle_number"] }}" min="1" max="{{ $data["max_bottle_number"] }}" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <button type="button" id="printByBottleNumberBtn" data-type="1" class="btn btn-primary btn-sm btn-block"><i class="feather icon-printer mr-2"></i> Print Small</button>
                        </div>
                        <div class="col">
                            <button type="button" id="printByBottleNumberBtn" data-type="2" class="btn btn-primary btn-sm btn-block"><i class="feather icon-printer mr-2"></i> Print Big</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col">
        <form id="printByBlockNumber">
            <div class="card">
                <div class="card-body text-center pb-0">
                    <i class="fas fa-box text-c-blue d-block f-30"></i>
                    <h5 class="m-t-20"><span class="text-c-blue"></span><i class="fas fa-qrcode text-dark mr-2"></i>Print By Block Number</h5>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>From Number:</label>
                                <input name="from_number" type="number" value="1" min="1" max="{{ $data["max_block_number"] }}" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>To Number:</label>
                                <input name="to_number" type="number" value="{{ $data['max_block_number'] }}" min="1" max="{{ $data["max_block_number"] }}" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" id="printByBlockNumberBtn" class="btn btn-primary btn-sm btn-block"><i class="feather icon-printer mr-2"></i> Print</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col">
        <form id="printByWorker">
            <div class="card">
                <div class="card-body text-center pb-0">
                    <i class="fas fa-users text-c-blue d-block f-30"></i>
                    <h5 class="m-t-20"><span class="text-c-blue"></span><i class="fas fa-qrcode text-dark mr-2"></i>Print By Worker</h5>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Choose Worker:</label>
                                <select name="tc_worker_id" class="form-control form-control-sm">
                                    @foreach ($data["workers"] as $item)
                                        <option value="{{ $item['tc_worker_id'] }}">{{ $item['tc_workers']['code'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="card-footer">
                    <button type="button" id="printByWorkerBtn" class="btn btn-primary btn-sm btn-block"><i class="feather icon-printer mr-2"></i> Print</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col">
        <span id="alert-area-c"></span>
        <div class="card">

            <div class="card-body">
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="bg-light border border-left-0 border-right-0 py-2 border-top-0 pl-2"><i class="fas fa-qrcode text-dark mr-2"></i>Print By Checklist<i class="feather icon-check-square float-right mr-2 text-dark"></i></h5>
                    </div>
                    <div class="col-md-7">
                        
                        <div class="dt-responsive table-responsive">
                            
                            <table id="myTable" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr class="text-center align-middle">
                                        <th>Block Number</th>
                                        <th>Bottle Number</th>
                                        <th>Worker</th>
                                        <th>Option</th>
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

                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-danger btn-sm btn-block trigger-uncheck-all"><i class="feather icon-trash-2 mr-2"></i> Delete All</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-primary btn-sm btn-block trigger-print-check"><i class="feather icon-printer mr-2"></i>Print</button>
                            </div>
                        </div>
                        <hr>
                        
                        <span id="dataPrintCustom"></span>
                        
                        <hr>
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-danger btn-sm btn-block trigger-uncheck-all"><i class="feather icon-trash-2 mr-2"></i>Delete All</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-primary btn-sm btn-block trigger-print-check"><i class="feather icon-printer mr-2"></i>Print</button>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

