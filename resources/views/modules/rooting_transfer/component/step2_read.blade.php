<div class="row">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">Step 2 (Data Observation)</span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="modifyStep2">
                <span class="badge badge-danger rounded-0"><i class="feather icon-edit-2 mr-1"></i>Modify</span>
            </a>
        </h5>
    </div>
    <div class="col text-right">
        <h4><i class="fas fa-check-circle text-primary"></i></h4>
    </div>
</div>
<table class="table table-xs table-bordered text-center">
    <thead>
        <tr>
            <th>Bottle Date</th>
            <th>Type</th>
            <th>Alphabetic</th>
            <th>Media Bottle</th>
            <th>First Total</th>
            <th>Work Bottle</th>
            <th>Work Plantlet</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dtSession as $item)
            <tr>
                <td>{{ $item['bottle_date'] }}</td>
                <td>{{ $item['bottle_type'] }}</td>
                <td>{{ $item['alpha'] }}</td>
                <td>{{ $item['media'] }}</td>
                <td>{{ $item['first_total'] }}</td>
                <td>{{ $item['work_bottle'] }}</td>
                <td>{{ $item['work_leaf'] }}</td>
            </tr>
        @endforeach
        <tr class="bg-light">
            <th colspan="5" class="text-right">Total:</th>
            <th>{{ $data['total'] }}</th>
            <th>{{ $data['totalLeaf'] }}</th>
        </tr>
    </tbody>
</table>

@include('modules.rooting_transfer.component.include.step2_read_js')
