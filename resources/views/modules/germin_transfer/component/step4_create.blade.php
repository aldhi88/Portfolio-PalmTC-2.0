<div class="row align-items-center">
    <div class="col"><h5><span class="badge badge-secondary rounded-0">Step 4 (Back & Out Bottle)</span></h5></div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-muted"></i></h4></div>
</div>
<form id="finishStep4">@csrf
<div class="row">
<div class="col">
    <table class="table table-xs table-bordered text-center">
        <thead>
            <tr>
                <th>Bottle<br>Date</th>
                <th>Alphabetic</th>
                <th>Work<br>Bottle</th>
                <th>Back Bottle</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dtSession as $item)
                <tr>
                    <td>{{ $item['bottle_date'] }}</td>
                    <td>{{ $item['alpha'] }}</td>
                    <td>{{ $item['work_bottle'] }}</td>
                    <td>
                        <input type="number" name="input_{{ $item['id'] }}" min="0" value="{{ isset($item['back_bottle'])?$item['back_bottle']:0 }}" max="{{ $item['work_bottle'] }}">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
<div class="row">
    <div class="col text-right">
        <button type="submit" class="btn btn-sm btn-primary">Finish Step 4</button>
    </div>
</div>
</form>
@include('modules.germin_transfer.component.include.step4_create_js')