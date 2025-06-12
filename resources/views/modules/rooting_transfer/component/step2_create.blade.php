<div class="row">
    <div class="col">
        <h5><span class="badge badge-secondary rounded-0">Step 2 (Data Observation)</span></h5>
    </div>
    <div class="col text-right">
        <h4><i class="fas fa-check-circle text-muted"></i></h4>
    </div>
</div>

<span id="alert-area-step2"></span>
<div class="row">

    <div class="col">
        <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
            <thead>
                <tr>
                    <th rowspan="2">Bottle<br>Date</th>
                    <th rowspan="2">Type</th>
                    <th rowspan="2">Alpha</th>
                    <th rowspan="2">Media</th>
                    <th colspan="2" class="text-center">Total</th>
                    <th rowspan="2">Working<br>Plantlet</th>
                </tr>
                <tr>
                    <th><i class="fas fa-vials"></i></th>
                    <th><i class="fas fa-seedling"></i></th>
                </tr>
                <thead id="header-filter" class="bg-white">
                    <tr>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white"></th>
                        <th class="bg-white" disable="true"></th>
                        <th class="bg-white" disable="true"></th>
                        <th class="bg-white" disable="true"></th>
                    </tr>
                </thead>
            </thead>
            <tbody>
                @foreach ($data['bottles'] as $item)
                    @php
                        $total = $item->bottle_left;
                        $used = 0;
                        foreach ($dtSession as $key => $value) {
                            if($item->id == $value['id']){
                                $used = $value['work_bottle'];
                            }
                        }
                        $bottleLeft = $total-$used;

                        $total = $item->leaf_left;
                        $used = 0;
                        foreach ($dtSession as $key => $value) {
                            if($item->id == $value['id']){
                                $used = $value['work_leaf'];
                            }
                        }
                        $leafLeft = $total-$used;
                    @endphp
                    <tr>
                        <td>{{ $item->bottle_date_format }}</td>
                        <td>{{ $item->tc_rooting_bottles->bottle_type }}</td>
                        <td>{{ $item->tc_rooting_bottles->alpha }}</td>
                        <td>{{ $item->tc_rooting_bottles->tc_bottles->code }}</td>
                        <td>{{ $bottleLeft }}</td>
                        <td>{{ $leafLeft }}</td>
                        <td>
                            <form class="addObs">@csrf
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <input type="hidden" name="bottle_date" value="{{ $item->bottle_date_format }}">
                                <input type="hidden" name="sub" value="{{ $item->tc_rooting_bottles->sub }}">
                                <input type="hidden" name="bottle_type" value="{{ $item->tc_rooting_bottles->bottle_type }}">
                                <input type="hidden" name="alpha" value="{{ $item->tc_rooting_bottles->alpha }}">
                                <input type="hidden" name="type" value="{{ $item->tc_rooting_bottles->type }}">
                                <input type="hidden" name="media" value="{{ $item->tc_rooting_bottles->tc_bottles->code }}">
                                <input type="hidden" name="first_total" value="{{ $item->bottle_left }}">
                                <input type="hidden" name="first_leaf" value="{{ $item->leaf_left }}">
                                <div class="input-group">
                                    <input type="number" name="work_leaf" required="" min="1" max="{{ $leafLeft }}" value="1" class="form-control form-control-sm">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-plus"></i></button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col">
        <table class="table table-xs table-bordered text-center" id="myTable2">
            <thead>
                <tr>
                    <th rowspan="2">Bottle<br>Date</th>
                    <th rowspan="2">Type</th>
                    <th rowspan="2">Alpha</th>
                    <th rowspan="2">Media</th>
                    <th colspan="2">Work</th>
                    <th rowspan="2" class="text-center">Delete</th>
                </tr>
                <tr>
                    <th><i class="fas fa-vials"></i></th>
                    <th><i class="fas fa-seedling"></i></th>
                </tr>
            </thead>
            <tbody>
                @if (count($dtSession)==0)
                    <tr><td colspan="5" class="text-center">Please select worker data first.</td></tr>
                @else
                    @foreach ($dtSession as $item)
                        <tr>
                            <td>{{ $item['bottle_date'] }}</td>
                            <td>{{ $item['bottle_type'] }}</td>
                            <td>{{ $item['alpha'] }}</td>
                            <td>{{ $item['media'] }}</td>
                            <td>{{ $item['work_bottle'] }}</td>
                            <td>{{ $item['work_leaf'] }}</td>
                            <td>
                                <form class="delObs">@csrf @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
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
            <button type="submit" class="btn btn-sm btn-primary">Finish Step 2</button>
        </form>
    </div>
</div>

@include('modules.rooting_transfer.component.include.step2_create_js')
