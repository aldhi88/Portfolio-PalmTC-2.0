<span id="alert-area-step1"></span>
<div class="row align-items-center">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">
                Step 1 (Initiation)
            </span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="edit-initiation-btn">
                <span class="badge badge-danger rounded-0"><i class="feather icon-edit-2 mr-1"></i>Modify</span>
            </a>
        </h5>
    </div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-success"></i></h4></div>
</div>
<div class="row">
    <div class="col">
        <div class="bg-light border px-3 pb-0 pt-2">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-1 border-right border-bottom">
                                <label><strong>Sampling: </strong></label>
                                <span>{{ $dtStep1['sample_number_show'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-1 border-right border-bottom">
                                <label><strong>Initiation Date: </strong></label>
                                <span>{{ $dtStep1['created_at_show'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-1 border-right border-bottom">
                                <label><strong>Working Date: </strong></label>
                                <span>{{ $dtStep1['date_work_show'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-1 border-right">
                                <label><strong>Block: </strong></label>
                                <span>{{ $dtStep1['number_of_block'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-1 border-right">
                                <label><strong>Bottle/Block: </strong></label>
                                <span>{{ $dtStep1['number_of_bottle'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-1 border-right">
                                <label><strong>Explant/Bottle: </strong></label>
                                <span>{{ $dtStep1['number_of_plant'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-1 border-right">
                                <label><strong>Room: </strong></label>
                                <span>{{ $dtStep1['room_code'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="col">
                    <div class="form-group mb-1">
                        <label class="mb-0"><strong>Note/Desc</strong></label><br>
                        <span>{{ $dtStep1['desc'] }}</span>
                    </div>
                </div>
            </div>
                        
        </div>
    </div>
</div>

@include('modules.init.include.step1_read_js')