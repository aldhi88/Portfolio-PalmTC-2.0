@extends('layouts.master')
@section('css')
@include('modules.callus_ob.include.create_by_sample_css')
@endsection

@section('js')
@include('modules.callus_ob.include.create_by_sample_js')
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col border-right">
                        <a href="{{ route('callus-obs.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>All Data</a>
                        <a href="{{ route('callus-obs.show',$data['init']->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Detail</a>
                    </div>
                    <div class="col text-center border-right" style="top: 5px">
                        <h5>Sample : </h5>
                        <span class="badge-light-primary px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i>{{ $data['init']->tc_samples->sample_number_display }}
                        </span>
                    </div>
                    <div class="col text-center border-right" style="top: 5px">
                        <h5>Last Obs : </h5>
                        <span class="badge-light-primary px-2 py-1">
                            <i class="fas fa-calendar-check mr-2"></i>{{ $data['lastObs'] }}
                        </span>
                    </div>
                    <div class="col text-center" style="top: 5px">
                        <h5>Number Of : </h5>
                        <span class="badge-light-primary px-2 py-1">
                            <i class="fas fa-tags mr-2"></i>{{ $data['countObs'] }}
                        </span>
                    </div>
                </div>
        </div>
            
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <span id="alert-area"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-md-4"><h5 style="top: 5px"><i class="feather icon-file-text"></i> All Bottles</h5></div>
                    <div class="col">
                        <form id="startObs">@csrf
                        <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <div class="col text-right mt-1">
                                            <label class="font-weight-bold">Observation Data:</label>
                                        </div>
                                        <select name="tc_worker_id" class="form-control form-control-sm mt-1">
                                            @foreach ($data['workers'] as $item)
                                                <option {{ $item->id==$data['worker_now']?'selected':null }} value="{{ $item->id }}">{{ $item->code }}</option>
                                            @endforeach
                                        </select>
                                        <input type="date" name="date_ob" value="{{ $data['date_ob'] }}" class="form-control form-control-sm mt-1">
                                        <input type="hidden" name="id" value="{{ $data['obsId'] }}">
                                        <input type="hidden" name="action" value="{{ !$data['start']?'start':'update' }}">
                                        <button name="start" class="{{ !$data['start']?null:'d-none ' }}btn btn-primary btn-sm rounded-0" type="submit">Start</button>
                                        <button name="update" class="{{ !$data['start']?'d-none ':null }}btn btn-secondary btn-sm rounded-0" type="submit">Change</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row {{ !$data['start']?null:'d-none' }}" id="msg-table-hide">
                    <div class="col">
                        <div class="alert alert-info text-center" role="alert">
							Choose worker and date working, then click "<strong>Start</strong>" button to start observation process.
						</div>
                    </div>
                </div>

                <div class="row {{ !$data['start']?'d-none ':null }}" id="table-wrap">
                    <div class="col">
                        <input type="hidden" name="tc_init_id" value="{{ $data['initId'] }}">
                        <input type="hidden" name="tc_callus_ob_id" value="{{ $data['obsId'] }}">
                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th>Worker</th>
                                    <th>Block</th>
                                    <th>Bottle</th>
                                    <th>Form</th>
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

@endsection

