<form id="formDeleteModal"> @csrf @method('DELETE')
    <input type="hidden" name="id">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-alert-octagon"></i> Delete Confirmation Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <h6 id="attr-data">...</h6>
            <p>Are you sure you delete this data?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-danger">Confirm Delete</button>
        </div>
    </div>
</form>