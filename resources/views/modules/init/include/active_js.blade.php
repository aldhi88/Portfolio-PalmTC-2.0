<script>
    function genReactiveData(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("inits.getDataDelete") }}',
            data: {id:id},
            success: (a) => {
                $("#date-identifier-active").text(a.data.return.init_date);
                $("#sample-identifier-active").text(a.data.return.sample_number);
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
    $("#activeModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        $("#activeForm input[name='id']").val(id);
        genReactiveData(id);
    });
    
    $('#activeForm').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#activeForm input[name='id']").val();
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ route('inits.active') }}",
            data: formData,
            success: (a) => {
                dtTable.ajax.reload();
                $('#activeModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                showAlert('danger', 'times', 'alert-area', a.status);
                $('#activeModal').modal('toggle');
                loader(false);
            }
        });
    });
</script>

