<style>
    .dataTables_filter { 
        display: none; 
    }
    #myMediumTableFilter th{
        background-color: white !important;
    }
</style>
<div class="row align-items-center">
    <div class="col"><h5><span class="badge badge-secondary rounded-0">Step 3 (Stock)</span></h5></div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-muted"></i></h4></div>
</div>
<span id="alert-area-step3"></span>
<div class="row">
    <div class="col-md-7">
        <table id="myMediumTable" class="table table-xs table-striped table-bordered">
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
            <thead id="myMediumTableFilter">
                <tr>
                    <th>...</th>
                    <th>...</th>
                    <th>...</th>
                    <th>...</th>
                    <th class="text-right" disable="true"></th>
                    <th disable="true" width="100"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data["medium_stocks"] as $item)
                    @if ($item->current_stock != 0)
                        <tr>
                            <td>{{ $item->created_at_short_format }}</td>
                            <td>{{ $item->tc_mediums->code }}</td>
                            <td>{{ $item->tc_bottles->code }}</td>
                            <td>{{ $item->tc_agars->code }}</td>
                            <td class="text-right">
                                @php
                                    $collect = collect($data['session']['data']);
                                    $count = $collect->where('tc_medium_stock_id',$item->id)->count();
                                    $currentStock = $item->current_stock;
                                    if($count!=0){
                                        $usedStock = $collect->firstWhere('tc_medium_stock_id',$item->id);
                                        $usedStock = $usedStock['used_stock'];
                                        $currentStock = $currentStock - $usedStock;
                                    }
                                    echo number_format($currentStock,0,',','.')
                                @endphp
                            </td>
                            <td>
                                <form class="addInitStock"> @csrf
                                    <input type="hidden" name="tc_medium_stock_id" value="{{ $item->id }}">
                                    <input type="hidden" name="medium_code" value="{{ $item->tc_mediums->code }}">
                                    <div class="input-group">
                                        <input type="number" name="used_stock" placeholder="0" required min="1" max="{{ $currentStock }}" class="form-control form-control-sm">
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
                    <th>Date</th>
                    <th class="text-center" style="font-size: 11px;line-height: 100%">Medium<br>Bottle<br>Agar</th>
                    <th class="text-right">Stock</th>
                    <th class="text-center">Delete</th>
                </tr>
            </thead>
            <tbody>
                @if (count($data['session']['data']) == 0)
                    <tr><td colspan="5" class="text-center">Please add Worker first.</td></tr>
                @else
                    @foreach ($data['session']['data'] as $item)
                        <tr>
                            <td>{{ $loop->index+1 }}</td>
                            <td>{{ $item['stock_date'] }}</td>
                            <td style="font-size: 11px;line-height: 100%">
                                {{ $item['medium'] }} <br>
                                {{ $item['bottle'] }} <br>
                                {{ $item['agar'] }}
                            </td>
                            <td class="text-right">{{ $item['used_stock'] }}</td>
                            <td class="text-center">
                                <form class="delStockInit"> @csrf @method('DELETE')
                                    <input type="hidden" name="tc_medium_stock_id" value="{{ $item['tc_medium_stock_id'] }}">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="feather icon-trash-2"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    
                @endif
                {{-- <tr class="font-weight-bold bg-light">
                    <td colspan="3" class="text-right py-1">Total Bottle ({{ $data['load'] }}) :</td>
                    <td class="text-right py-1">{{ $data['total'] }}</td>
                    <td class=" py-1"></td>
                </tr> --}}
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col text-right">
        <form id="step3Form"> @csrf
            <button type="submit" class="btn btn-primary btn-sm">Finish Step 3</button>
        </form>
    </div>
</div>

@include('modules.init.include.step3_create_js')