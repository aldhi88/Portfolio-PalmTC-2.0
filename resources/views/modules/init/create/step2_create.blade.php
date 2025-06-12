<style>
    .dataTables_filter { 
        display: none; 
    }
    #myTableFilter th{
        background-color: white !important;
    }
</style>
<div class="row align-items-center">
    <div class="col"><h5><span class="badge badge-secondary rounded-0">Step 2 (Worker)</span></h5></div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-muted"></i></h4></div>
</div>
<span id="alert-area-step2"></span>
<div class="row">
    <div class="col-md-6">
        <table id="myTable" class="table table-xs table-striped table-bordered">
            <thead>
                <tr>
                    <th>Worker Code</th>
                    <th></th>
                </tr>
            </thead>
            <thead id="myTableFilter">
                <tr>
                    <th>Worker Code</th>
                    <th disable="true"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data["workers"] as $item)
                    @if (in_array($item->id, array_column($data['sessionWorkers'], 'tc_worker_id')) != true)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>
                                <form class="addWorkerForm">@csrf
                                    <input type="hidden" name="tc_worker_id" value="{{ $item->id }}">
                                    <input type="hidden" name="worker_code" value="{{ $item->code }}">
                                    <div class="input-group">
                                        <select name="tc_laminar_id" class="form-control form-control-sm mr-2">
                                            @foreach ($data["laminars"] as $laminarItem)
                                                <option value="{{ $laminarItem->id }}">{{ $laminarItem->code }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-plus"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col">
        <table class="table table-xs table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Worker Code</th>
                    <th>Laminar</th>
                    <th class="text-center">Delete</th>
                </tr>
            </thead>
            <tbody>
                @if (count($data['sessionWorkers']) == 0)
                    <tr><td colspan="5" class="text-center">Please select worker data first.</td></tr>
                @else
                    @foreach ($data['sessionWorkers'] as $item)
                        <tr>
                            <td>{{ $loop->index+1 }}</td>
                            <td>{{ $item['worker_code'] }}</td>
                            <td>{{ $item['laminar_code'] }}</td>
                            <td class="text-center">
                                <form class="delWorkerInitiation"> @csrf @method('DELETE')
                                    <input type="hidden" name="tc_worker_id" value="{{ $item['tc_worker_id'] }}">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="feather icon-trash-2"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col text-right">
        <form id="finishStep2">@csrf
            <button type="submit" class="btn btn-primary btn-sm">Finish Step 2</button>
        </form>
    </div>
</div>

@include('modules.init.include.step2_create_js')