<div id="deleteCommentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-plus"></i> Delete Confirm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="delComment"> @csrf @method('DELETE')
                <div class="modal-body">
                    <h5>Are you sure you delete this data?</h5>
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Comment</strong></label>
                                <p class="comment"></p>
                            </div>
                        </div>
                        {{-- <div class="col">
                            <div class="form-group">
                                <label><strong>Date</strong></label>
                                <p></p>
                            </div>
                        </div> --}}
                    </div>
                    {{-- <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><strong>File</strong></label>
                                <input type="hidden" name="old_file" value="{{$data['data_edit']->file}}">
                                <p>{{$data['data_edit']->file}}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><strong>Image</strong></label>
                                <input type="hidden" name="old_image" value="{{$data['data_edit']->image}}">
                                @if (!is_null($data['data_edit']->image))
                                <img class="img-fluid" src="{{asset('storage/media/sample/image/'.$data['data_edit']->image)}}" alt="">
                                @endif
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('pushJs')
<script>
    $('#delComment').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('inits.commentDestroy') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                $('#deleteCommentModal').modal('hide');
                dtComment.ajax.reload();
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidate('#delComment');
                    $.each(a.responseJSON.errors, function(key, value){
                        showValidate('#delComment',key, value);
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
