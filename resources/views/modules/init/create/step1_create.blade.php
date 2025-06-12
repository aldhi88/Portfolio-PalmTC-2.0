<div class="row align-items-center">
    <div class="col"><h5><span class="badge badge-secondary rounded-0">Step 1 (Initiation)</span></h5></div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-muted"></i></h4></div>
</div>

<form id="formStep1"> @csrf
    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label><strong>Sampling</strong></label>
                        <div class="input-group">
                            <input type="text" name="sample_number_display" value="{{ $data['last_sample']->sample_number_display }}" readonly name="resample_display" class="form-control form-control-sm px-1">
                            <input type="hidden" name="tc_sample_id" value="{{ $data['last_sample']->id }}">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary btn-sm btn-block" data-toggle="modal" data-target="#sampleModal"><i class="feather mr-1 icon-search"></i>Select</button>
                            </div>
                        </div>
                        <small><span class="text-danger sample_number_display msg"></span></small>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Initiation Date</strong></label>
                        <input type="date" value="{{ date('Y-m-d') }}" name="created_at" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Working Date</strong></label>
                        <input type="date" value="{{ date('Y-m-d') }}" name="date_work" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><strong>Block</strong></label>
                        <input type="number" minlength="1" value="60" name="number_of_block" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Bottle/Block</strong></label>
                        <input type="number" minlength="1" value="8" name="number_of_bottle" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><strong>Explant/Bottle</strong></label>
                        <input type="number" minlength="" value="3" name="number_of_plant" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong>Room</strong></label>
                        <select name="tc_room_id" class="form-control form-control-sm">
                            @foreach ($data['rooms'] as $item)
                                <option value="{{ $item->id }}">{{ $item->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label><strong>Note/Desc</strong></label>
                <textarea placeholder="Type here your note/description.." name="desc" class="form-control form-control-sm border px-2 pb-3" rows="4"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col text-right">
            <button class="btn btn-primary btn-sm" type="submit">Finish Step 1</button>
        </div>
    </div>

</form>

<div id="sampleModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content"></div>
    </div>
</div>

@include('modules.init.include.step1_create_js')
