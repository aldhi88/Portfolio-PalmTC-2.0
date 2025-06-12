<script>
    var dtTable = $('#myMediumTable').DataTable({
        searching: true,
        lengthChange: false,
        pageLength: 5,
        aaSorting: [],
        columnDefs: [
            { orderable: false, targets: [4,5] }
        ],
        initComplete: function () {
            $('#myMediumTableFilter th').each(function() {
                var title = $(this).text();
                var disable = $(this).attr("disable");
                if(disable!="true"){
                    $(this).html('<input placeholder="'+title+'" type="text" class="form-control medium-column-search px-1 form-control-sm"/>');
                }
            });
            $('#myMediumTableFilter').on('keyup', ".medium-column-search",function () {
                dtTable.column( $(this).parent().index() ).search( this.value ).draw();
            });
        }
    });

    addInitStock();
    function addInitStock(){
        $('table#myMediumTable tbody').on('submit','form.addInitStock',function(e){
            loader(true);
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('inits.addStock') }}",
                data: formData,
                cache: false, contentType: false, processData: false,
                success: (a) => {
                    if(a.status=="error"){
                        showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                    }else{
                        genStep3();
                    }
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
    }

    $('#step3Form').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('inits.finishStep3') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                if(a.status == 'error'){
                    showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                }else{
                    genStep3();
                }
                
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
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

    $('.delStockInit').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ route('inits.delStock') }}",
            data: formData,
            success: (a) => {
                genStep3();
                loader(false);
            },
            error: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            }
        });
    });
</script>

