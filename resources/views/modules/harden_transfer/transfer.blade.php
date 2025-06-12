<form id="formTransferModal"> @csrf
    <input type="hidden" name="tc_harden_ob_id">
    <input type="hidden" name="max">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-alert-octagon"></i> Transfer Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <span id="alert-area-modal-transfer"></span>
            <div class="row">
                <div class="col">
                    <h5>Harden Date: <span id="harden-date"></span></h5>
                </div>
                <div class="col">
                    <h5>Obs Date: <span id="obs-date"></span></h5>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Worker</label>
                        <select name="tc_worker_id" class="form-control form-control-sm">
                            @foreach ($data['worker'] as $item)
                                <option value="{{ $item->id }}">{{ $item->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Transfer Date</label>
                        <input type="date" name="transfer_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Back to Harden</label>
                        <input type="number" value="0" min="0" class="form-control form-control-sm" name="to_self">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Transfer to Nursery</label>
                        <input type="number" value="0" min="0" class="form-control form-control-sm" name="to_next">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Process Transfer</button>
        </div>
    </div>
</form>