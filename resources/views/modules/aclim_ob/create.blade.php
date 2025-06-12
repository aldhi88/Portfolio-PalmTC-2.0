@extends('layouts.master')
@section('css')
@include('modules.aclim_ob.include.create_css')
@endsection

@section('js')
@include('modules.aclim_ob.include.create_js')
@endsection

@section('content')

<div class="row">
    <div class="col">
        <span id="alert-area"></span>

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a href="{{ route('aclim-obs.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>All Data</a>
                        <a href="{{ route('aclim-obs.show',$data['initId']) }}" class="btn btn-warning btn-sm"><i class="fas fa-backward mr-2"></i>Detail</a>
                        <span class="ml-3">
                            <h5>Sample : </h5> </i>{{ $data['sample'] }}
                        </span>
                    </div>
                    <div class="col-md-8">
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
                                        <input type="date" name="date_ob" value="{{ !$data['date_ob']?date('Y-m-d'):$data['date_ob'] }}" class="form-control form-control-sm mt-1">
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
                <span id="alert-area2"></span>
                <div class="row {{ !$data['start']?null:'d-none' }}" id="msg-table-hide">
                    <div class="col">
                        <div class="alert alert-info text-center" role="alert">
							Choose worker and date working, then click "<strong>Start</strong>" button to start observation process.
						</div>
                    </div>
                </div>

                <div class="row {{ !$data['start']?'d-none ':null }}" id="table-wrap">
                    <div class="col">
                        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                            <thead>
                                <tr>
                                    <th rowspan="2">Acclim<br>Date</th>
                                    <th rowspan="2">Number</th>
                                    <th colspan="2" class="text-center">Form Observation</th>
                                </tr>
                                <tr>
                                    <th>Death</th>
                                    <th>Transfer<br>
                                        <div class="form-group form-check m-0 p-0">
                                            <input type="checkbox" {{ $data['isCheck'] }} class="form-check-input checkbox" id="exampleCheck1" value="all">
                                            <label class="form-check-label mt-1" for="exampleCheck1">Check all</label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <thead id="header-filter2" class="bg-white">
                                <tr>
                                    <th class="bg-white" disable="true"></th>
                                    <th class="bg-white"></th>
                                    <th class="bg-white" disable="true"></th>
                                    <th class="bg-white" disable="true"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@endsection

