@extends('layouts.master')
@section('css')
@include('modules.liquid_transfer.include.show_css')
@endsection

@section('js')
@include('modules.liquid_transfer.include.show_js')
@include('modules.liquid_transfer.include.delete_js')
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
                        <a href="{{ route('liquid-transfers.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Transfer Summary</a>
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
                                    <label><strong>Bottle Embryo: </strong></label><br>{{ $data['sumEmbryo'] }}
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
                        <a href="{{ route('liquid-transfers.create',$data['initId']) }}" class="btn btn-sm btn-primary"><i class="fas fa-exchange-alt"></i> New Transfer</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th>Transfer <br> Date</th>
                                    <th>Worker</th>
                                    <th>Laminar</th>
                                    <th>Alpha</th>
                                    <th>Cycle</th>
                                    <th>To Liquid</th>
                                    <th>To Maturation</th>
                                    <th>#</th>
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
                        <a href="{{ route('liquid-transfers.create',$data['initId']) }}" class="btn btn-sm btn-primary"><i class="fas fa-exchange-alt"></i> New Transfer</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th>Bottle <br>Date</th>
                                    <th>Obs <br> Date</th>
                                    <th>Alpha</th>
                                    <th>Cycle</th>
                                    <th>First<br>Total</th>
                                    <th>Has Been<br>Transfer</th>
                                    <th>Bottle<br>Ready</th>
                                    <th>Bottle<br>Back</th>
                                    <th>Bottle<br>Out</th>
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

@include('modules.liquid_transfer.include.modal')


@endsection

