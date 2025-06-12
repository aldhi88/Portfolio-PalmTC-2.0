<script>
    function generateDataDelete(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("rooting-transfers/parsingDataDel") }}/'+id,
            success: (a) => {
                $('#formDelModal #attr-data').text('').text(a.attr);
                $('#formDelModal input[name="id"]').val(a.id);
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
    $("#delModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateDataDelete(id);
    });
    $('#formDelModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#formDelModal input[name='id']").val();
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: '{{ url("rooting-transfers") }}/'+key,
            data: formData,
            success: (a) => {
                if(a.status == 'success'){
                    dtTable.ajax.reload();
                    dtTable2.ajax.reload();
                }
                $('#delModal').modal('toggle');
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

