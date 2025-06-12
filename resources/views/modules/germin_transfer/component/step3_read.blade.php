<div class="row">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">Step 3 (Transfer Process)</span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="modifyStep3">
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
        <div class="bg-light border px-3 pb-0 pt-2">
            
            <div class="row">
                <div class="col text-center">
                    <div class="form-group mb-1 border-right">
                        <label><strong>To Germination: </strong></label>
                        <span>{{ $dtSession['to_back'] }}</span>
                    </div>
                </div>
                <div class="col text-center">
                    <div class="form-group mb-1">
                        <label><strong>To Rooting: </strong></label>
                        <span>{{ $dtSession['to_next'] }} <i class="fas fa-leaf"></i> : {{ $dtSession['leaf_count'] }}</span>
                    </div>
                </div>
                
            </div>
                
        </div>
    </div>
</div>

@include('modules.germin_transfer.component.include.step3_read_js')