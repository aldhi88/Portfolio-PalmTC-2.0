<script>
    var dtTable = $('#myTable').DataTable({
        searching: true,
        lengthChange: false,
        pageLength: 5,
        columnDefs: [
            { orderable: false, targets: [1] }
        ],
        initComplete: function () {
            $('#myTableFilter th').each(function() {
                var title = $(this).text();
                var disable = $(this).attr("disable");
                if(disable!="true"){
                    $(this).html('<input placeholder="'+title+'" type="text" class="form-control mytable-column-search px-1 form-control-sm"/>');
                }
            });
            $('#myTableFilter').on('keyup', ".mytable-column-search",function () {
                dtTable.column( $(this).parent().index() ).search( this.value ).draw();
            });
        }
    });

    $('table#myTable tbody').on('submit','form.addWorkerForm',function(e){
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('inits.addWorker') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                genStep2();
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
                }
                loader(false);
            }
        });
    });

    $('#finishStep2').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ route('inits.finishStep2') }}",
            data: formData,
            success: (a) => {
                if(a.status == 'error'){
                    showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                }else{
                    genStep2();
                    genStep3();
                }
                
                loader(false);
            },
            error: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            }
        });
    });

    $('.delWorkerInitiation').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ route('inits.delWorker') }}",
            data: formData,
            success: (a) => {
                genStep2();
                loader(false);
            },
            error: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            }
        });
    });
</script>

