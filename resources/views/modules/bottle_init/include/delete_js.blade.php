<script>
    $("#deleteModal").on("show.bs.modal", function(e) {
        var data = jQuery.parseJSON(e.relatedTarget.dataset.json);
        $('#formDeleteModal #attr-data').text('').text(data.name);
        $('#formDeleteModal input[name="id"]').val(data.id);
    });
    $('#formDeleteModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#formDeleteModal input[name='id']").val();
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ url('bottle-inits') }}/"+key,
            data: formData,
            success: (a) => {
                if(a.status == 'success'){
                    dtTable.ajax.reload();
                }
                $('#deleteModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            }
        });
    });
</script>

