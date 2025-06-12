@extends('layouts.master')
@section('css')
@include('modules.embryo_transfer.include.show_css')
@endsection

@section('js')
@include('modules.embryo_transfer.include.show_js')
@include('modules.embryo_transfer.include.delete_js')
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
                        <a href="{{ route('embryo-transfers.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Transfer Summary</a>
                    </div>
                    <div class="col text-right">
                        <h5>Sample:</h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i>{{ $data['sampleNumber'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="row text-center">
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
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col-md-3 text-right">
                                <label class="font-weight-bold mt-2">Page Number:</label>
                            </div>
                            <div class="col text-right">
                                <div class="input-group">
                                    <input type="number" min="1" required value="1" name="page" class="form-control form-control-sm" placeholder="Number of row">
                                    <button id="btnPrint" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Form Transfer to Germin & Liquid</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col-md-3 text-right">
                                <label class="font-weight-bold mt-2">Page Number:</label>
                            </div>
                            <div class="col text-right">
                                <div class="input-group">
                                    <input type="number" min="1" required value="1" name="page" class="form-control form-control-sm" placeholder="Number of row">
                                    <button id="btnPrint2" class="btn btn-info btn-sm rounded-0 mr-1"><i class="feather mr-2 icon-printer"></i>Form Transfer to Embryogenesis</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-3"><h5 class="mt-2"><i class="feather icon-file-text"></i> All Transfer Data</h5></div>


                    @if ($data['allowTransfer'])
                    <div class="col text-right">
                        <a href="{{ route('embryo-transfers.create',$data['initId']) }}" class="btn btn-sm btn-primary"><i class="fas fa-exchange-alt"></i> New Transfer</a>
                    </div>
                    @endif
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
                                    <th>To<br>58</th>
                                    <th>To<br>Germination</th>
                                    <th>To<br>Liquid</th>
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
                        @if ($data['allowTransfer'])
                            <a href="{{ route('embryo-transfers.create',$data['initId']) }}" class="btn btn-sm btn-primary"><i class="fas fa-exchange-alt"></i> New Transfer</a>
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
                                    <th>Bottle <br>Date</th>
                                    <th>Obs <br> Date</th>
                                    <th>Sub<br>Culture</th>
                                    <th>First<br>Total</th>
                                    <th>Work<br>Bottle</th>
                                    <th>Bottle<br>Back</th>
                                    <th>Transferred<br>Bottle</th>
                                    <th>Need<br>Transfer</th>
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

@include('modules.embryo_transfer.include.modal')


@endsection

