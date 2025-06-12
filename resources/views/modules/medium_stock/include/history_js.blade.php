<script>
    function generateDataHistory(id = 0){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("medium-stocks/get-history") }}/'+id,
            success: (a) => {
                $('#historyData').html(a);
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }

    $("#historyModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateDataHistory(id);
    });


</script>