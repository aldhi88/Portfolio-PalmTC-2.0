<form id="addSubtForm"> @csrf
    <input type="hidden" name="tc_embryo_bottle_id">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="feather icon-minus"></i> Modify Bottle Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label><strong>Total Botol</strong></label>
                <input name="bottle_count" type="number" min="1" max="" value="1" class="form-control form-control-sm focus">
                <small><span class="code text-danger bottle_count"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Reason</strong></label>
                <input name="reason" type="text" class="form-control form-control-sm">
                <small><span class="name text-danger reason"></span></small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Add Subtraction</button>
        </div>
    </div>
</form>