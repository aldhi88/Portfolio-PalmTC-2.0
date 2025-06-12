@extends('layouts.master')
@section('css')
@include('modules.rooting_transfer.include.show_css')
@endsection

@section('js')
@include('modules.rooting_transfer.include.show_js')
@include('modules.rooting_transfer.include.delete_js')
@endsection

@section('content')
{{-- print element --}}
<div id="exportPrint" class="d-none"></div>

<div class="row">
    <div class="col">
        <div class="card border border-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a href="{{ route('rooting-transfers.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Transfer Summary</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="row text-center">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Sample:&nbsp;</strong></label><br>{{ $data['sampleNumber'] }}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Transfer Count: </strong></label><br>{{ $data['transferCount'] }}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Bottle Rooting: </strong></label><br>{{ $data['sumRooting'] }}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Transferred: </strong></label><br>{{ $data['hasTransfer'] }}
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label><strong>Need Transfer: </strong></label><br>{{ $data['notTransfer'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col">
        <span id="alert-area-1"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> All Transfer Data</h5></div>
                    <div class="col text-right">
                        @if ($data['allowTransfer'])
                        <a href="{{ route('rooting-transfers.create',$data['initId']) }}" class="btn btn-sm btn-primary"><i class="fas fa-exchange-alt"></i> New Transfer</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th rowspan="2">Transfer <br> Date</th>
                                    <th rowspan="2">Worker</th>
                                    <th rowspan="2">Laminar</th>
                                    <th rowspan="2">Alpha</th>
                                    <th colspan="2" class="text-center">Rooting 1</th>
                                    <th rowspan="2">Rooting 2</th>
                                    <th rowspan="2">To Acclim</th>
                                    <th rowspan="2">#</th>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-vials"></i></th>
                                    <th><i class="fas fa-seedling"></i></th>
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

<div class="row">
    <div class="col">
        <span id="alert-area-sample"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> Summary Per Transfer Item</h5></div>
                    <div class="col text-right">
                        @if ($data['allowTransfer'])
                        <a href="{{ route('rooting-transfers.create',$data['initId']) }}" class="btn btn-sm btn-primary"><i class="fas fa-exchange-alt"></i> New Transfer</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th rowspan="2">Bottle <br>Date</th>
                                    <th rowspan="2">Obs <br> Date</th>
                                    <th rowspan="2">Alpha</th>
                                    <th colspan="2" class="text-center">First Total</th>
                                    <th colspan="2" class="text-center">Processed</th>
                                    <th colspan="2" class="text-center">Back</th>
                                    <th colspan="2" class="text-center">Out</th>
                                    <th colspan="2" class="text-center">Ready</th>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-vials"></i></th>
                                    <th><i class="fas fa-seedling"></i></th>
                                    <th><i class="fas fa-vials"></i></th>
                                    <th><i class="fas fa-seedling"></i></th>
                                    <th><i class="fas fa-vials"></i></th>
                                    <th><i class="fas fa-seedling"></i></th>
                                    <th><i class="fas fa-vials"></i></th>
                                    <th><i class="fas fa-seedling"></i></th>
                                    <th><i class="fas fa-vials"></i></th>
                                    <th><i class="fas fa-seedling"></i></th>
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

@include('modules.rooting_transfer.include.modal')


@endsection

