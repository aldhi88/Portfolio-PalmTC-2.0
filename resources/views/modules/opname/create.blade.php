<form id="opnameFormCreateModal"> @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Add Data Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label><strong>Media ID</strong></label>
                <input name="tc_media_id" readonly type="text" class="form-control form-control-sm">
                <small><span class="tc_media_id text-danger msg"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Media Name</strong></label>
                <input name="name" type="text" disabled class="form-control form-control-sm">
            </div>
            <div class="form-group">
                <label><strong>Media Code</strong></label>
                <input name="code" type="text" disabled class="form-control form-control-sm">
            </div>
            <div class="form-group">
                <label><strong>Stock In</strong></label>
                <input type="text" name="stock_in" class="form-control form-control-sm" placeholder="0">
                <small><span class="stock_in text-danger msg"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Stock Out</strong></label>
                <input type="text" name="stock_out" class="form-control form-control-sm" placeholder="0">
                <small><span class="stock_out text-danger msg"></span></small>
            </div>
    
            <div class="form-group">
                <label><strong>Description</strong></label>
                <textarea name="desc" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Data</button>
        </div>
    </div>
</form>