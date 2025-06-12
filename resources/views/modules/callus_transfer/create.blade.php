@extends('layouts.master')
@section('css')
@include('modules.callus_transfer.include.create_css')
@endsection

@section('js')
@include('modules.callus_transfer.include.create_js')
@endsection

@section('content')
{{-- print element --}}
<span id="alert-area"></span>
<div class="row">
    <div class="col">
        <div class="card border border-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-1">
                        <a href="{{ route('callus-transfers.detail',$data['initId']) }}" class="btn btn-warning btn-sm btn-block"><i class="fas fa-backward"></i></a>
                    </div>
                    <div class="col" style="top: 5px">
                        <h5>Sample : </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i> {{ $data['sampleNumber'] }}
                        </span>
                    </div>
                    <div class="col" style="top: 5px">
                        <h5>Obs Date : </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-calendar mr-2"></i>{{ $data['obsDate'] }}
                        </span>
                    </div>
                    <div class="col" style="top: 5px">
                        <h5>Total Bottle: </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-flask mr-2"></i> {{ $data['totalBottleCallus'] }}
                        </span>
                    </div>
                    <div class="col" style="top: 5px">
                        <h5>Bottle Left : </h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-flask mr-2 text-primary"></i> <span id="bottleLeft">{{ $data['bottleLeft'] }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <form id="createCallusTransfer">@csrf
                            <input type="hidden" name="tc_init_id" value="{{ $data['initId'] }}">
                            <input type="hidden" name="tc_callus_ob_id" value="{{ $data['obsId'] }}">
                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="font-weight-bold">Worker</label>
                                    <select name="tc_worker_id" class="form-control form-control-sm">
                                        @foreach ($data['worker'] as $item)
                                            <option value="{{ $item->id }}">{{ $item->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col">
                                    <label class="font-weight-bold">Laminar</label>
                                    <select name="tc_laminar_id" class="form-control form-control-sm">
                                        @foreach ($data['laminar'] as $item)
                                            <option value="{{ $item->id }}">{{ $item->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="font-weight-bold">Bottle Worked</label>
                                    <input type="number" value="0" max="{{ $data['bottleLeft'] }}" name="bottle_used" class="form-control form-control-sm">
                                    <small><span class="bottle_used text-danger msg"></span></small>
                                </div>
                                <div class="form-group col">
                                    <label class="font-weight-bold">New Bottle</label>
                                    <div class="input-group">
                                        <input type="number" readonly name="new_bottle" value="0" class="form-control form-control-sm px-2">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary btn-sm py-0" data-toggle="modal" data-target="#modalMediumStock">pick</button>
                                        </div>
                                    </div>
                                    <small><span class="new_bottle text-danger msg"></span></small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-7">
                                    <label class="font-weight-bold">Date</label>
                                    <input type="date" value="{{ now()->format('Y-m-d') }}" name="date_work" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="font-weight-bold">Time Work</label>
                                    <input type="number" name="time_work" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="font-weight-bold">Comment</label>
                                    <input type="text" name="comment" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <button id="submitFromTransfer" type="submit" {{ $data['bottleLeft']==0?'disabled':null }} class="btn btn-primary btn-block">Transfer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col">
                        <div class="form-row">
                            <div class="form-group col">
                                <select name="print_group" id="dateList" class="form-control form-control-sm">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="form-group col-2">
                                <button id="printByGroup" class="btn btn-primary btn-sm btn-block"><i class="feather icon-printer"></i> Print</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Worker</th>
                                        <th>Laminar</th>
                                        <th>Worked <br> Bottle</th>
                                        <th>New<br>Bottle</th>
                                        <th>Time<br>Work</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <thead id="header-filter" class="bg-white">
                                    <tr>
                                        <th class="bg-white" disable="true"></th>
                                        <th class="bg-white"></th>
                                        <th class="bg-white"></th>
                                        <th class="bg-white"></th>
                                        <th class="bg-white"></th>
                                        <th class="bg-white" disable="true"></th>
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
@include('modules.callus_transfer.include.modal')
@endsection

