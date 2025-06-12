<form id="formEditModal"> @csrf @method('PUT')
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-repeat"></i> Validation Stock Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <span id="alert-area-modal"></span>
            <input type="hidden" name="tc_medium_stock_id">
            <input type="hidden" name="id">
            <div class="bg-light border p-2 rounded mb-3">
                <div class="row">
                    <div class="col"><strong>Name : </strong><span id="name">...</span></div>
                    <div class="col"><strong>Date : </strong><span id="created_at">...</span></div>
                </div>
                <div class="row">
                    <div class="col"><strong>Added Stock : </strong><span id="addedStock">...</span></div>
                    {{-- <div class="col"><strong>Last Stock : </strong><span id="lastStock">...</span></div> --}}
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><strong>Stock In</strong></label>
                        <input name="stock_in" type="text" class="form-control form-control-sm" value="0">
                        <small><span class="stock_in text-danger msg"></span></small>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Stock Out</strong></label>
                        <input name="stock_out" type="text" class="form-control form-control-sm" value="0">
                        <small><span class="stock_out text-danger msg"></span></small>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label><strong>Description</strong></label>
                <textarea name="desc" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Save Data</button>
        </div>
    </div>
</form>