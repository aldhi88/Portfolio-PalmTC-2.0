<script>
    $("#createModal").on("shown.bs.modal", function(e) {
        $('.focus').focus();
    });
    $("#createModal").on("show.bs.modal", function(e) {
        resetFormCreate();
        clearValidationCreate();
    });
    function clearValidationCreate(){
        $('#formCreateModal input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#formCreateModal span.'+key).text(value);
        $('#formCreateModal input[name="'+key+'"]').addClass('is-invalid');
    }
    function resetFormCreate(){
        $('#formCreateModal').trigger('reset');
    }

    $('#formCreateModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('mediums.store') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                resetFormCreate();
                dtTable.ajax.reload();
                $('#createModal').modal('toggle');
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
                    $('#createModal').modal('toggle');
                }
                loader(false);
            }
        });
    });
</script>

