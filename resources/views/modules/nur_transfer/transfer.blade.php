<form id="formTransferModal"> @csrf
    <input type="hidden" name="tc_nur_ob_id">
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
                    <h5>Nursery Date: <span id="nur-date"></span></h5>
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
                <div class="col-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Back to Nursery</label>
                        <input type="number" value="0" min="0" class="form-control form-control-sm" name="to_self">
                    </div>
                </div>
                {{-- ---------------------- --}}
                <div class="col bg-light border">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">To Estate</label>
                                <input type="number" value="0" min="0" class="form-control form-control-sm" name="to_self2">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Block</label>
                                <input type="text" class="form-control form-control-sm" name="block_es">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Row</label>
                                <input type="text" class="form-control form-control-sm" name="row_es">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Tree</label>
                                <input type="text" class="form-control form-control-sm" name="tree_es">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Plantation</label>
                                <select name="plant_es" class="form-control form-control-sm">
                                    @foreach ($data['plant'] as $item)
                                        <option value="{{ $item->id }}">{{ $item->name.' ('.$item->code.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ---------------------- --}}
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">To Field</label>
                                <input type="number" value="0" min="0" class="form-control form-control-sm" name="to_next">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Block</label>
                                <input type="text" class="form-control form-control-sm" name="block_f">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Row</label>
                                <input type="text" class="form-control form-control-sm" name="row_f">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Tree</label>
                                <input type="text" class="form-control form-control-sm" name="tree_f">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">Plantation</label>
                                <select name="plant_f" class="form-control form-control-sm">
                                    @foreach ($data['plant'] as $item)
                                        <option value="{{ $item->id }}">{{ $item->name.' ('.$item->code.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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