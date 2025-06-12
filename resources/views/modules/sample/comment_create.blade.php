<div id="addCommentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Add New Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="createComment"> @csrf
                <div class="modal-body">
                    <input type="hidden" name="tc_sample_id" value="{{$data['data_edit']->id}}">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Comment</strong></label>
                                <input name="comment" required type="text" class="form-control form-control-sm">
                                <small><span class="comment text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Date</strong></label>
                                <input name="created_at" value="{{date('Y-m-d')}}" type="date" class="form-control form-control-sm">
                                <small><span class="created_at text-danger msg"></span></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>File</strong></label>
                                <input name="file" type="file" class="form-control form-control-sm">
                                <small><span class="file text-danger msg"></span></small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Image</strong></label>
                                <input name="image" type="file" class="form-control form-control-sm">
                                <small><span class="image text-danger msg"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col text-right">
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('pushJs')

<script>
    $('#createComment').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('samples.commentStore') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                $('#addCommentModal').modal('hide');
                dtComment.ajax.reload();
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidate('#createComment');
                    $.each(a.responseJSON.errors, function(key, value){
                        showValidate('#createComment',key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area', a.status);
                    $('#createModal').modal('toggle');
                }
                loader(false);
            }
        });
    });
</script>

@endpush

