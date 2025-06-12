<div class="row">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">Step 4 (Back & Out Bottle)</span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="modifyStep4">
                <span class="badge badge-danger rounded-0"><i class="feather icon-edit-2 mr-1"></i>Modify</span>
            </a>
        </h5>
    </div>
    <div class="col text-right">
        <h4><i class="fas fa-check-circle text-primary"></i></h4>
    </div>
</div>
<div class="row">
<div class="col">
    <table class="table table-xs table-bordered text-center">
        <thead>
            <tr>
                <th>Obs<br>Date</th>
                <th>Alphabetic</th>
                <th>Work<br>Bottle</th>
                <th>Back Bottle</th>
                <th>Bottle Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dtSession as $item)
                <tr>
                    <td>{{ $item['bottle_date'] }}</td>
                    <td>{{ $item['alpha'] }}</td>
                    <td>{{ $item['work_bottle'] }}</td>
                    <td>{{ $item['back_bottle'] }}</td>
                    <td>{{ $item['work_bottle'] - $item['back_bottle'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@include('modules.germin_transfer.component.include.step4_read_js')