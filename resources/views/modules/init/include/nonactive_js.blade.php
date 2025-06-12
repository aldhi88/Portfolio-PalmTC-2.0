<script>
    function genDataActive(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("inits.getDataDelete") }}',
            data: {id:id},
            success: (a) => {
                $("#date-identifier-nonactive").text(a.data.return.init_date);
                $("#sample-identifier-nonactive").text(a.data.return.sample_number);
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
    $("#nonActiveModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        $("#nonActiveForm input[name='id']").val(id);
        genDataActive(id);
    });
    
    function clearValidationCreate(){
        $('#nonActiveForm input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#nonActiveForm span.'+key).text(value);
        $('#nonActiveForm input[name="'+key+'"]').addClass('is-invalid');
    }

    $('#nonActiveForm').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#nonActiveForm input[name='id']").val();
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ route('inits.nonActive') }}",
            data: formData,
            success: (a) => {
                dtTable.ajax.reload();
                $('#nonActiveModal').modal('toggle');
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
                    $('#nonActiveModal').modal('toggle');
                }
                loader(false);
            }
        });
    });
</script>

