<span id="alert-area-step1"></span>
<div class="row align-items-center">
    <div class="col"><h5><span class="badge badge-secondary rounded-0">Step 1 (Initiation)</span></h5></div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-muted"></i></h4></div>
</div>
<form id="formEditStep1"> @csrf
    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><strong>Sampling</strong></label>
                        <div class="input-group mb-3">
                            <input type="text" name="sample_number_display" value="{{ $dtStep1['sample_number_show'] }}" readonly class="form-control form-control-sm px-1">
                            <input type="hidden" name="tc_sample_id" value="{{ $dtStep1['tc_sample_id'] }}">
                            
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary btn-sm btn-block" data-toggle="modal" data-target="#sampleModal"><i class="feather mr-1 icon-search"></i>Select</button>
                            </div>
                            <small><span class="text-danger sample_number_display msg"></span></small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Initiation Date</strong></label>
                        <input type="date" value="{{ $dtStep1['created_at'] }}" name="created_at" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Working Date</strong></label>
                        <input type="date" value="{{ $dtStep1['date_work'] }}" name="date_work" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><strong>Block</strong></label>
                        <input type="number" minlength="1" value="{{ $dtStep1['number_of_block'] }}" name="number_of_block" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Bottle/Explant</strong></label>
                        <input type="number" minlength="1" value="{{ $dtStep1['number_of_bottle'] }}" name="number_of_bottle" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Explant/Bottle</strong></label>
                        <input type="number" minlength="" value="{{ $dtStep1['number_of_plant'] }}" name="number_of_plant" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Room</strong></label>
                        <select name="tc_room_id" class="form-control form-control-sm">
                            @foreach ($data['rooms'] as $item)
                                <option {{ $dtStep1['tc_room_id'] == $item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label><strong>Note/Desc</strong></label>
                <textarea placeholder="Type here your note/description.." name="desc" class="form-control form-control-sm border px-2 pb-3" rows="4">{{ $dtStep1['desc'] }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col text-right">
            <button class="btn btn-primary btn-sm" type="submit">Save Changes Step 1</button>
        </div>
    </div>
</form>

<div id="sampleModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content"></div>
    </div>
</div>

@include('modules.init.include.step1_edit_js')