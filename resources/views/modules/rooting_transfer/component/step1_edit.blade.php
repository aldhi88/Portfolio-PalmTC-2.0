<form id="editStep1">@csrf
    <div class="row">
        <div class="col">
            <h5><span class="badge badge-secondary rounded-0">Step 1 (Transfer Data)</span></h5>
        </div>
        <div class="col text-right">
            <h4><i class="fas fa-check-circle text-muted"></i></h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label class="font-weight-bold">Worker</label>
                <select name="tc_worker_id" class="form-control form-control-sm">
                    @foreach ($data['worker'] as $item)
                        <option {{ $item->id == $dtSession['tc_worker_id']?'selected':null }} value="{{ $item->id }}">{{ $item->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Laminar</label>
                <select name="tc_laminar_id" class="form-control form-control-sm">
                    @foreach ($data['laminar'] as $item)
                        <option {{ $item->id==$dtSession['tc_laminar_id']?'selected':null }} value="{{ $item->id }}">{{ $item->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label class="font-weight-bold">Transfer Date</label>
                <input type="date" name="transfer_date" class="form-control form-control-sm" value="{{ $dtSession['transfer_date'] }}">
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Work Time</label>
                <input type="number" value="{{ $dtSession['work_time'] }}" min="0" name="work_time" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label class="font-weight-bold">Alpha</label>
                <select name="alphaCycle" class="form-control form-control-sm">
                    @foreach ($data['subCultere'] as $item)
                        <option {{ $item->alpha == $dtSession['alpha']?'selected':null }} value="{{ $item->alpha }}">{{ $item->alpha }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Comment</label>
                <input type="text" name="comment" class="form-control form-control-sm">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col text-right">
            <button type="submit" class="btn btn-sm btn-primary">Finish Step 1</button>
        </div>
    </div>
</form>

@include('modules.rooting_transfer.component.include.step1_edit_js')