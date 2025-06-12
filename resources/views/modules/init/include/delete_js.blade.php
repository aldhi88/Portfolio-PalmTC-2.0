<script>
    function generateDataDelete(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("inits.getDataDelete") }}',
            data: {id:id},
            success: (a) => {
                $("#date-identifier").text(a.data.return.init_date);
                $("#sample-identifier").text(a.data.return.sample_number);
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
    $("#deleteInitModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        $("#deleteInitForm input[name='id']").val(id);
        $("#deleteInitForm input[name='pass_confirm']").val(null);
        generateDataDelete(id);
    });
    $("#deleteInitModal").on("shown.bs.modal", function(e) {
        $("#deleteInitForm input[name='pass_confirm']").focus();
    });
    
    function clearValidationCreate(){
        $('#deleteInitForm input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#deleteInitForm span.'+key).text(value);
        $('#deleteInitForm input[name="'+key+'"]').addClass('is-invalid');
    }

    $('#deleteInitForm').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#deleteInitForm input[name='id']").val();
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ url('inits') }}/"+key,
            data: formData,
            success: (a) => {
                dtTable.ajax.reload();
                $('#deleteInitModal').modal('toggle');
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
                    $('#deleteInitModal').modal('toggle');
                }
                loader(false);
            }
        });
    });

    $('#formImportModal').submit(function(e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('import.initsImport') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (a) => {
                $('#formImportModal').trigger('reset')
                if (a.status == 'success') {
                    dtTable.ajax.reload();
                }
                $('#importModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                showAlert('danger', 'times', 'alert-area', a.status);
                loader(false);
            }
        });
    });
</script>

