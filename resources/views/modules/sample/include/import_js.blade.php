<script>
    $('#formImportModal').submit(function(e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('import.sampleImport') }}",
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
