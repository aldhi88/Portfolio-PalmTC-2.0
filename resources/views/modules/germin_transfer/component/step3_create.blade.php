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
            <label class="font-wight-bold">To Germination</label>
            <div class="input-group">
                <input type="number" readonly="" name="to_back" value="0" class="form-control form-control-sm px-2">
                <div class="input-group-append">
                    <button data-id="back" type="button" class="btn btn-primary btn-sm py-0 has-ripple" data-toggle="modal" data-target="#modalMediumStock">Pick</button>
                </div>
            </div>
        </div>
        <div class="form-group col">
            <label class="font-wight-bold">To Rooting</label>
            <div class="input-group">
                <input type="number" readonly="" name="to_next" value="0" class="form-control form-control-sm px-2">
                <div class="input-group-append">
                    <button data-id="next" type="button" class="btn btn-primary btn-sm py-0 has-ripple" data-toggle="modal" data-target="#modalMediumStock">Pick</button>
                </div>
                <div class="input-group-append">
                    <input type="number" name="leaf_count" min="1" placeholder="Explant Count" class="form-control form-control-sm px-2 border">
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

@include('modules.germin_transfer.component.include.step3_create_js')
@include('modules.germin_transfer.component.include.modal')