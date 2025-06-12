@extends('layouts.master')

@section('css')
    @include('modules.callus_transfer.include.index_print_label_css')
@endsection

@section('js')
@include('modules.callus_transfer.include.index_print_label_js')
@endsection

@section('content')

<div class="row">
    <div class="col">
        <span id="alert-area"></span>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <a href="{{ route('callus-transfers.detailTransfer',$data['init_id']) }}" class="btn btn-warning btn-sm d-none d-sm-inline"><i class="feather mr-2 icon-skip-back"></i>Back to Detail Transfer</a>
                        <a href="{{ route('callus-transfers.detailTransfer',$data['init_id']) }}" class="btn btn-warning btn-sm btn-block d-sm-none"><i class="feather mr-2 icon-skip-back"></i>Back to Detail Transfer</a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <span id="alert-area-b"></span>
                @if ($data['bottle_count'] != $data['bottle_transferred'])
                <div class="alert alert-danger text-center rounded-0" role="alert">
                    <i class="feather icon-alert-circle" style="font-size: 30px"></i><br>
                    The transfer process has not been completed, there are still leftover bottles that can be transfer
                </div>
                @endif
                <div class="row">
                    <div class="col">
                        <div class="bg-light border px-3 pb-0 pt-2">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group mb-1 border-right border-bottom">
                                                <label><strong>Sampling: </strong>{{ $data['sampling_no'] }}</label>
                                                <span></span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-right border-bottom">
                                                <label><strong>Initiation Date: </strong>{{ $data['init_date'] }}</label>
                                                <span></span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-bottom border-right">
                                                <label><strong>Observation Date: </strong>{{ $data['obs_date'] }}</label>
                                                <span></span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-bottom">
                                                <label><strong>Bottle Callus: </strong>{{ $data['bottle_count'] }}</label>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group mb-1 border-right">
                                                <label>
                                                    <strong>Bottle Print: </strong>{{ $data['bottle_transferred'] }}
                                                </label>
                                                <span></span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-right">
                                                <label><strong>Worker Count: </strong>{{ $data['worker_count'] }}</label>
                                                <span></span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1 border-right">
                                                <label><strong>Start Number: </strong>{{ $data['start_no'] }}</label>
                                                <span></span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-1">
                                                <label><strong>End Number: </strong>{{ $data['end_no'] }}</label>
                                                <span></span>
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
                                <input name="from_number" type="number" value="{{ $data['start_no'] }}" min="{{ $data['start_no'] }}" max="{{ $data['end_no'] }}" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>To Number:</label>
                                <input name="to_number" type="number" value="{{ $data['end_no'] }}" min="{{ $data['start_no'] }}" max="{{ $data['end_no'] }}" class="form-control form-control-sm focus">
                                <small><span class="code text-danger msg"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" id="printByBottleNumberBtn" class="btn btn-primary btn-sm btn-block"><i class="feather icon-printer mr-2"></i> Print</button>
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
                                    @foreach ($data["worker"] as $item)
                                        <option value="{{ $item->tc_worker_id }}">{{ $item->tc_workers->code }}</option>
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
                    <div class="col">
                        
                        <div class="dt-responsive table-responsive">
                            
                            <table id="myTable" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr class="text-center align-middle">
                                        <th>Block</th>
                                        <th>No. Bottle</th>
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
                        {{-- list --}}
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

