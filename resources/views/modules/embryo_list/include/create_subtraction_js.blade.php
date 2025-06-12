<script>
    $("#addSubtModal").on("shown.bs.modal", function(e) {
        $('.focus').focus();
    });
    $("#addSubtModal").on("show.bs.modal", function(e) {
        var tc_embyro_bottle_id = $(e.relatedTarget).data('id');
        var max = $(e.relatedTarget).data('max');
        $('#addSubtForm input[name="tc_embryo_bottle_id"]').val(tc_embyro_bottle_id);
        $('#addSubtForm input[name="bottle_count"]').attr('max',max);
        resetFormCreate();
        clearValidationCreate();
    });
    function clearValidationCreate(){
        $('#addSubtForm input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#addSubtForm span.'+key).text(value);
        $('#addSubtForm input[name="'+key+'"]').addClass('is-invalid');
    }
    function resetFormCreate(){
        $('#addSubtForm').trigger('reset');
    }

    $('#addSubtForm').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('embryo-lists.storeSubtraction') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                resetFormCreate();
                dtTable.ajax.reload();
                $('#addSubtModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area', a.status);
                    $('#addSubtModal').modal('toggle');
                }
                loader(false);
            }
        });
    });
</script>