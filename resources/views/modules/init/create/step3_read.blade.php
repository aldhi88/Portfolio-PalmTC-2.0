<div class="row align-items-center">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">Step 3 (Stock)</span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="edit-init-stock-btn">
                <span class="badge badge-danger rounded-0"><i class="feather icon-edit-2 mr-1"></i>Modify</span>
            </a>
        </h5>
    </div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-success"></i></h4></div>
    
</div>
<div class="row">
    <div class="col">
        <table class="table table-xs table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Medium</th>
                    <th>Bottle</th>
                    <th>Agar</th>
                    <th class="text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($data['session']['data'] as $item)
                @php
                    $total = $total + $item['used_stock'];
                @endphp
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $item['stock_date'] }}</td>
                        <td>{{ $item['medium'] }}</td>
                        <td>{{ $item['bottle'] }}</td>
                        <td>{{ $item['agar'] }}</td>
                        <td class="text-right">{{ $item['used_stock'] }}</td>
                    </tr>
                @endforeach
                <tr class="font-weight-bold bg-light">
                    <td colspan="5" class="text-right py-1">Bottle Total :</td>
                    <td class="text-right py-1">{{ $total }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@include('modules.init.include.step3_read_js')