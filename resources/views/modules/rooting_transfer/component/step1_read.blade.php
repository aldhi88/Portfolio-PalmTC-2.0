<div class="row">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">Step 1 (Transfer Data)</span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="modifyStep1">
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
                <div class="col">
                    <div class="form-group mb-1 border-right border-bottom">
                        <label><strong>Worker: </strong></label>
                        <span>{{ $dtSession['workerCode'] }}</span>
                    </div>
                    <div class="form-group mb-1 border-right">
                        <label><strong>Laminar: </strong></label>
                        <span>{{ $dtSession['laminarCode'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mb-1 border-right border-bottom">
                        <label><strong>Transfer Date: </strong></label>
                        <span>{{ $dtSession['transferDate'] }}</span>
                    </div>
                    <div class="form-group mb-1 border-right">
                        <label><strong>Work Time: </strong></label>
                        <span>{{ $dtSession['work_time'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mb-1">
                        <label class="mb-0"><strong>Alphabetic:</strong></label>
                        <span>{{ $dtSession['alpha'] }}</span>
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0"><strong>Comment:</strong></label><br>
                        <span>{{ $dtSession['comment'] }}</span>
                    </div>
                </div>
            </div>
                
        </div>
    </div>
</div>

@include('modules.rooting_transfer.component.include.step1_read_js')