<script>
  $('#form-import').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('import.importCallus') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                $('#form-import').trigger('reset')
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