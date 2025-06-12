<span id="alert-step3"></span>
<form id="createStep3">@csrf
    <div class="row">
        <div class="col">
            <h5><span class="badge badge-secondary rounded-0">Step 3 (Transfer Process)</span></h5>
        </div>
        <div class="col text-right">
            <h4><i class="fas fa-check-circle text-muted"></i></h4>
        </div>
    </div>
    <div class="row">
        <div class="form-group col">
            <label class="font-wight-bold">Back to Embryo List</label>
            <div class="input-group">
                <input type="number" readonly="" name="to_callus" value="{{ $dtSession['to_callus'] }}" class="form-control form-control-sm px-2">
                <div class="input-group-append">
                    <button data-id="callus" type="button" class="btn btn-primary btn-sm py-0 has-ripple" data-toggle="modal" data-target="#modalMediumStock">Pick</button>
                </div>
            </div>
        </div>
        <div class="form-group col">
            <label class="font-wight-bold">Solid / To Germination</label>
            <div class="input-group">
                <input type="number" readonly="" name="to_solid" value="{{ $dtSession['to_solid'] }}" class="form-control form-control-sm px-2">
                <div class="input-group-append">
                    <button data-id="solid" type="button" class="btn btn-primary btn-sm py-0 has-ripple" data-toggle="modal" data-target="#modalMediumStock">Pick</button>
                </div>
            </div>
        </div>
        <div class="form-group col">
            <label class="font-wight-bold">Suspension / To Liquid</label>
            <div class="input-group">
                <input type="number" readonly="" name="to_suspen" value="{{ $dtSession['to_suspen'] }}" class="form-control form-control-sm px-2">
                <div class="input-group-append">
                    <button data-id="suspen" type="button" class="btn btn-primary btn-sm py-0 has-ripple" data-toggle="modal" data-target="#modalMediumStock">Pick</button>
                </div>
            </div>
        </div>
    
    </div>
    <div class="row">
        <div class="col text-right">
            <button type="submit" class="btn btn-sm btn-primary">Finish Step 3</button>
        </div>
    </div>
</form>

@include('modules.embryo_transfer.component.include.step3_create_js')
@include('modules.embryo_transfer.component.include.modal')