<script>
    $("#viewSubtModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateData(id);
    });

    function generateData(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("embryo-lists.showSubtraction") }}',
            data: {
                id:id
            },
            success: (a) => {
                $('#modal-content').html(a);
                initFormDelete();
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }

    function initFormDelete(){
        $('.delSubtraction').submit(function (e) {
            loader(true);
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('embryo-lists.destroySubtraction') }}",
                data: formData,
                cache: false, contentType: false, processData: false,
                success: (a) => {
                    generateData(a.data.id);
                    dtTable.ajax.reload();
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
    }
    
</script>