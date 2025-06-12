<div id="deleteInitModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="deleteInitForm"> @csrf @method('DELETE')
                <input type="hidden" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-alert-octagon"></i> Delete Confirmation Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger rounded-0">
                            <div class="row">
                                <div class="col-1">
                                    <h4><i class="feather icon-alert-triangle"></i></h4>
                                </div>
                                <div class="col">
                                    <strong>Deleting this data will also delete other related data.</strong><br>
                                    Are you sure you delete this data?
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <h6 class="mb-0">Initiation Date:</h6>
                                <span id="date-identifier">...</span>
                            </div>
                            <div class="col">
                                <h6 class="mb-0">Sample Number:</h6>
                                <span id="sample-identifier">...</span>
                            </div>
                        </div>
            
                        <div class="row mt-3">
                            <div class="col">
                                <div class="form-group">
                                    <label class="font-weight-bold">Password Required</label>
                                    <input type="password" name="pass_confirm" class="form-control form-control-sm" placeholder="Type your login password for continue.">
                                    <small><span class="pass_confirm text-danger msg"></span></small>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger">Confirm Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="importModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Import Data From Excel File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formImportModal">@csrf
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        Pada proses ini, setiap baris data di excel akan dibuatkan 480 baris data botol, maka proses import akan sangat lama. Untuk 1 baris data excel menghabiskan waktu 20 detik.
                    </div>
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
    </div>
</div>