<script>
    
    $("#stockValidateModal").on("shown.bs.modal", function(e) {
        $('.focus').focus();
    });
    $("#stockValidateModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateData(id);
        resetFormCreate();
        clearValidationCreate();
    });

    function generateData(id){
        $('#formCreateModal input[name="tc_medium_stock_id"]').val(id);
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("medium-stocks/get-data") }}/'+id,
            success: (a) => {
                var data = a.data.data[0];
                $('#formCreateModal #name').text('').text(data.tc_mediums.name);
                $('#formCreateModal #created_at').text('').text(data.format_created_at);
                $('#formCreateModal span#addedStock').text('').text(data.stock);
                $('#formCreateModal span#lastStock').text('').text(data.last_stock);
            },
            error: (a) => {
                alert("Error #005, please contact your admin.");
            }
        });
    }
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
        var stockin = $('#formCreateModal input[name="stock_in"]').val();
        var stockout = $('#formCreateModal input[name="stock_out"]').val();
        if((stockin == 0 && stockout == 0) || (stockin != 0 && stockout != 0)){
            showAlert('danger', 'times', 'alert-area-modal', 'Stock In / Stock Out is invalid.');
            return false;
        }
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('medium-validate.store') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                resetFormCreate();
                initDt();
                $('#stockValidateModal').modal('toggle');
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

