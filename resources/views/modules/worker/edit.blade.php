<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-edit-1"></i> Change Data Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <form id="formEditModal">@csrf @method('PUT')
        <input type="hidden" name="id">
        <div class="modal-body">
            <div class="form-group">
                <label><strong>No. Pekerja</strong></label>
                <input name="no_pekerja" type="text" class="form-control form-control-sm focus">
                <small><span class="no_pekerja text-danger msg"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Name</strong></label>
                <input name="name" type="text" class="form-control form-control-sm">
                <small><span class="name text-danger msg"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Date of Birth</strong></label>
                <input name="date_of_birth" type="date" value="{{ '1990-01-01' }}" class="form-control form-control-sm focus">
                <small><span class="date_of_birth text-danger msg"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Code</strong></label>
                <input name="code" type="text" class="form-control form-control-sm">
                <small><span class="name text-danger msg"></span></small>
            </div>
            <div class="form-group">
                <label><strong>Status</strong></label>
                <select name="status" class="form-control form-control-sm">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
        </div>
    </form>
</div>