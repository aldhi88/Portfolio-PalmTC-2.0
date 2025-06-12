<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Import Data From Excel File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <form id="formImportModal">@csrf
        <div class="modal-body">
            
            <div class="form-group">
                <label><strong>Choose File</strong></label>
                <input name="file" type="file" class="form-control form-control-sm">
                <small><span class="file text-danger msg"></span></small>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Import</button>
        </div>
    </form>
</div>