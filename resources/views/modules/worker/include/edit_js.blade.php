<script>
    $("#editModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateDataEdit(id);
        resetValidationEdit();
    });

    function generateDataEdit(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("workers/get-data") }}/'+id,
            success: (a) => {
                var data = a.data.data[0];
                $('#formEditModal input[name="id"]').val(data.id);
                $('#formEditModal input[name="no_pekerja"]').val(data.no_pekerja);
                $('#formEditModal input[name="name"]').val(data.name);
                $('#formEditModal input[name="date_of_birth"]').val(data.date_of_birth);
                $('#formEditModal input[name="code"]').val(data.code);
                $("select option:selected").removeAttr('selected');
                $("select[name='status'] option[value='"+data.status+"']").attr("selected", true);
                
            },
            error: (a) => {
                alert("Error #001, please contact your admin.");
            }
        });
    }

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
            url: '{{ url("workers") }}/'+key,
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

