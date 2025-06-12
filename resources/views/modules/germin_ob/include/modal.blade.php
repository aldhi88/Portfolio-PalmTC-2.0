<div id="delModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="feather icon-align-center"></i> Delete Confirm Dialog</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <h6>Are you sure delete this data?</h6>
                <span id="attr"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button>
                <form id="formDel">@csrf @method('DELETE')
                    <input type="hidden" name="id">
                    <button type="submit" class="btn btn-danger btn-sm">Yes, Delete!</button>
                </form>
            </div>
        </div>
        
    </div>
</div>