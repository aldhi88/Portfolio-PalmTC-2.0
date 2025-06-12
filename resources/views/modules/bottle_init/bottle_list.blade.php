<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-alert-octagon"></i> Delete Confirmation Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <label class="font-weight-bold">Column Name: {{ $data['bottleInit']->name }}</label>
        @foreach ($data['bottles'] as $item)
        <div class="form-check">
            <input data-id="{{ $data['bottleInit']->id }}" class="form-check-input checkbox" {{ in_array($item->id,$data['bottleList'])?'checked':null }} type="checkbox" name="tc_bottle_id" value="{{ $item->id }}" id="check{{ $item->id }}">
            <label class="form-check-label" for="check{{ $item->id }}">{{ $item->code }}</label>
        </div>
        @endforeach
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Finish & Close</button>
    </div>
</div>
