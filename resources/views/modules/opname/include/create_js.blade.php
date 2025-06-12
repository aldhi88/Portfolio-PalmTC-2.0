<script>
    $("#opnameCreateModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateDataEdit(id);
        resetValidationEdit();
    });

    function generateDataEdit(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("medias/get-data") }}/'+id,
            success: (a) => {
                var data = a.data.data[0];
                $('#opnameFormCreateModal input[name="tc_media_id"]').val(data.id);
                $('#opnameFormCreateModal input[name="code"]').val(data.tc_master_medias.code);
                $('#opnameFormCreateModal input[name="name"]').val(data.tc_master_medias.name);
            },
            error: (a) => {
                alert("Error #005, please contact your admin.");
            }
        });
    }

    // $("#createModal").on("show.bs.modal", function(e) {
    //     resetFormCreate();
    //     clearValidationCreate();
    // });

    function clearValidationCreate(){
        $('#opnameFormCreateModal input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#opnameFormCreateModal span.'+key).text(value);
        $('#opnameFormCreateModal input[name="'+key+'"]').addClass('is-invalid');
    }
    function resetFormCreate(){
        $('#opnameFormCreateModal').trigger('reset');
    }

    $('#opnameFormCreateModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('opname.store') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                resetFormCreate();
                initDt($('input[name="key"]').val());
                $('#opnameCreateModal').modal('toggle');
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

