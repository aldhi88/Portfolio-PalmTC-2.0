<script>
    $("#transferModal").on("show.bs.modal", function(e) {
        $('#alert-area-modal-transfer').empty();
        var obsId = $(e.relatedTarget).data('id');
        var val = $(e.relatedTarget).data('val');
        var aclimDate = $(e.relatedTarget).data('aclimdate');
        var obsDate = $(e.relatedTarget).data('obsdate');
        $('#formTransferModal').trigger('reset');
        $('input[name="tc_aclim_ob_id"]').val(obsId);
        $('input[name="to_next"]').val(val);
        $('input[name="max"]').val(val);
        $('#aclim-date').text(aclimDate);
        $('#obs-date').text(obsDate);
    });

    $('#formTransferModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('tc_aclim_ob_id',$("input[name='tc_aclim_ob_id']").val());
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: '{{ route("aclim-transfers.store") }}',
            data: formData,
            success: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                if(a.status != 'error'){
                    $('#transferModal').modal('toggle');
                    dtTable.ajax.reload();
                    dtTable2.ajax.reload();
                }
                loader(false);
            },
            error: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            }
        });
    });
</script>

