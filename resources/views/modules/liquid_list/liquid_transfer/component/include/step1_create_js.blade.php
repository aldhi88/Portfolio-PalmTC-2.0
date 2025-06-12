<script>
    $('#createStep1').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('liquid-transfers.finishStep1') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                genStep1();genStep2();
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area-step1', a.status);
                }
                loader(false);
            }
        });
    });
</script>