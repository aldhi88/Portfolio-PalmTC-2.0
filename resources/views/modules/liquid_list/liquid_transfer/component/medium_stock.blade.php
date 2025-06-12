<style>
    #myTableFilter th, #myTableFilter th::placeholder{
        background-color: white;
    }

    .stock-search::placeholder{
        font-size: 10px;
    }

    .dataTables_filter { 
        display: none; 
    }
</style>
<div class="col">
    <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
        <thead>
            <tr>
                <th>Date</th>
                <th>Medium</th>
                <th>Bottle</th>
                <th>Agar</th>
                <th class="text-center">Stock</th>
                <th width="100"></th>
            </tr>
        </thead>
        <thead id="myTableFilter" class="bg-white">
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th disable="true"></th>
                <th disable="true" width="100"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['medStock'] as $item)
                @php
                    $total = $item['current_stock'];
                    $usedBack = 0;
                    $usedNext = 0;
                    foreach ($data['medStockBack'] as $key => $value) {
                        if($item['id'] == $value['id']){
                            $usedBack = $value['used_stock'];
                        }
                    }
                    foreach ($data['medStockNext'] as $key => $value) {
                        if($item['id'] == $value['id']){
                            $usedNext = $value['used_stock'];
                        }
                    }
                    $bottleLeft = $total-$usedBack-$usedNext;
                @endphp
                <tr>
                    <td>{{ $item['created_at_short_format'] }}</td>
                    <td>{{ $item['tc_mediums']['code'] }}</td>
                    <td>{{ $item['tc_bottles']['code'] }}</td>
                    <td>{{ $item['tc_agars']['code'] }}</td>
                    <td>{{ $bottleLeft }}</td>
                    <td>
                        <form class="addStock">@csrf
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                <input type="hidden" name="date" value="{{ $item['created_at_short_format'] }}">
                                <input type="hidden" name="medium" value="{{ $item['tc_mediums']['code'] }}">
                                <input type="hidden" name="bottle" value="{{ $item['tc_bottles']['code'] }}">
                                <input type="hidden" name="agar" value="{{ $item['tc_agars']['code'] }}">
                                <input type="hidden" name="for" value="{{ $data['for'] }}">
                                <div class="input-group">
                                    <input type="number" value="1" min="1" max="{{ $bottleLeft }}" name="used_stock" class="form-control form-control-sm">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary btn-sm py-0 has-ripple">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="col" style="padding-top: 14px">
    <h6>Used Stock List</h6>
    <table class="table table-striped table-bordered nowrap table-xs w-100 text-center" id="myTable2">
        <thead>
            <tr>
                <th>Date</th>
                <th>Medium</th>
                <th>Bottle</th>
                <th>Agar</th>
                <th class="text-center">Used</th>
                <th width="100"></th>
            </tr>
        </thead>
        <tbody>
            @if (count($data['medStockPicked'])==0)
                <tr class="text-center"><td colspan="6">No data available in table</td></tr>
            @else
                @foreach ($data['medStockPicked'] as $item)
                    <tr>
                        <td>{{ $item['date'] }}</td>
                        <td>{{ $item['medium'] }}</td>
                        <td>{{ $item['bottle'] }}</td>
                        <td>{{ $item['agar'] }}</td>
                        <td>{{ $item['used_stock'] }}</td>
                        <td>
                            <form class="delStock">@csrf @method('DELETE')
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                <input type="hidden" name="for" value="{{ $data['for'] }}">
                                <button class="btn btn-sm btn-danger">
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
