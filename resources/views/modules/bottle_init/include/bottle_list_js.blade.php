<script>
    $("#bottleListModal").on("show.bs.modal", function(e) {
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("bottle-inits.getDataBottle") }}',
            data:{id:$(e.relatedTarget).data('id')},
            success: (a) => {
                $('#bottleList').html(a);
                initChecked();
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    });

    
    function initChecked(){
        $('.checkbox').change(function() {
            var bottleId = $(this).val();
            var bottleInitId = $(this).attr('data-id');
            if(this.checked) {
                actionChecked(bottleId,bottleInitId,'in');
            }else{
                actionChecked(bottleId,bottleInitId,'del');
            }
        });
    }

    function actionChecked(bottleId,bottleInitId,action){
        $.ajax({
            type: 'POST', cache: false, processData: true,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, //note!
            url: '{{ route("bottle-inits.actionChecked") }}',
            data: {
                tc_bottle_id:bottleId,
                tc_bottle_init_id:bottleInitId,
                action:action
            },
            success: (a) => {
                dtTable.ajax.reload();
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
</script>

