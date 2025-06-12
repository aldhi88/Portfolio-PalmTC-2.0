<script>
var dtTable = $('#myTable').DataTable({
    searching: true,
    lengthChange: false,
    pageLength: 10,
    order: [[0,'desc']],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
        { orderable: false, targets: [1,2,3,4] },
    ],
    initComplete: function () {
        $('#header-filter th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control mytable-column-search px-1 form-control-sm"/>');
            }
        });
        $('#header-filter').on('keyup', ".mytable-column-search",function () {
            dtTable.column( $(this).parent().index() ).search( this.value ).draw();
        });
        addItemStep2();
    }
});


function addItemStep2(){
    $('table#myTable tbody').on('submit','form.addObs',function(e){
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('rooting-transfers.addItemStep2') }}",
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
}

$('table#myTable2 tbody').on('submit','form.delObs',function(e){
    loader(true);
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ route('rooting-transfers.delItemStep2') }}",
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
        type: 'POST',
        url: "{{ route('rooting-transfers.finishStep2') }}",
        data: formData,
        cache: false, contentType: false, processData: false,
        success: (a) => {
            if(a.status == 'error'){
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
            }else{
                genStep2();genStep3();
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
</script>