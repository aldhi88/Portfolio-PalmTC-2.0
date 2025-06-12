<script>
    $("#editModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateDataEdit(id);
        resetValidationEdit();
    });

    function generateDataEdit(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("medium-validate/get-data") }}/'+id,
            success: (a) => {
                var data = a.data.data[0];
                $('#formEditModal input[name=tc_medium_stock_id]').val(data.tc_medium_stock_id);
                $('#formEditModal input[name=id]').val(data.id);
                $('#formEditModal input[name=stock_in]').val(data.stock_in);
                $('#formEditModal input[name=stock_out]').val(data.stock_out);
                $('#formEditModal textarea[name=desc]').text(data.desc);
                $('#formEditModal #name').text('').text(data.tc_medium_stocks.tc_mediums.name);
                $('#formEditModal #created_at').text('').text(data.tc_medium_stocks.created_at_long_format);
                $('#formEditModal span#addedStock').text('').text(data.tc_medium_stocks.stock);
            },
            error: (a) => {
                alert("Error #005, please contact your admin.");
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
        var stockin = $('#formEditModal input[name="stock_in"]').val();
        var stockout = $('#formEditModal input[name="stock_out"]').val();
        if((stockin == 0 && stockout == 0) || (stockin != 0 && stockout != 0)){
            showAlert('danger', 'times', 'alert-area-modal', 'Stock In / Stock Out is invalid.');
            return false;
        }
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#formEditModal input[name='id']").val();
        $.ajax({
            type: 'POST',
            url: '{{ url("medium-validate") }}/'+key,
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

