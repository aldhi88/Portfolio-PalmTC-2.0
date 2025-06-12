<div id="nonActiveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="nonActiveForm"> @csrf
                <input type="hidden" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-alert-octagon"></i> Non Active Confirmation Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
        
                        <div class="row">
                            <div class="col">
                                <h6 class="mb-0">Initiation Date:</h6>
                                <span id="date-identifier-nonactive">...</span>
                            </div>
                            <div class="col">
                                <h6 class="mb-0">Sample Number:</h6>
                                <span id="sample-identifier-nonactive">...</span>
                            </div>
                        </div>
            
                        <div class="row mt-3">
                            <div class="col">
                                <div class="form-group">
                                    <label class="font-weight-bold">Date of Stop</label>
                                    <input type="date" name="date_stop" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                    <small><span class="date_stop text-danger msg"></span></small>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger">Yes Confirm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>