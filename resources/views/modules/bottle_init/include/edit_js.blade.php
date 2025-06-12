<script>
    $("#editModal").on("show.bs.modal", function(e) {
        var data = jQuery.parseJSON(e.relatedTarget.dataset.json);
        $('#formEditModal input[name="column_name"]').val(data.name);
        $('#formEditModal input[name="id"]').val(data.id);
        resetValidationEdit();
    });

    function resetValidationEdit(){
        $('#formEditModal input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationEdit(key, value){
        $('#formEditModal span.'+key).text(value);
        $('#formEditModal input[name="'+key+'"]').addClass('is-invalid');
    }
    $('#formEditModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#formEditModal input[name='id']").val();
        $.ajax({
            type: 'POST',
            url: '{{ url("bottle-inits") }}/'+key,
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                if(a.status == 'success'){
                    dtTable.ajax.reload();
                }
                $('#editModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    resetValidationEdit();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationEdit(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area', a.status);
                    $('#edit').modal('toggle');
                }

                loader(false);
            }
        });
    });
</script>

