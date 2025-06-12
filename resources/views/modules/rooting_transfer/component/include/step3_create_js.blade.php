<script>
$('#createStep3').submit(function (e) {
    loader(true);
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ route('rooting-transfers.finishStep3') }}",
        data: formData,
        cache: false, contentType: false, processData: false,
        success: (a) => {
            if(a.status == 'error'){
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
            }else{
                genStep3(); genStep4();
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
                showAlert('danger', 'times', 'alert-area-step3', a.status);
            }
            loader(false);
        }
    });
});

$("#modalMediumStock").on("show.bs.modal", function(e) {
    var id = $(e.relatedTarget).data('id');
    getMedStockTable(id);
});

function getMedStockTable(id){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("rooting-transfers.getMedStock") }}',
        data: {for:id},
        success: (a) => {
            $('#medStockArea').empty().html(a);
            initMedStock();
        },
        error: (a) => {
            alert("Error #003, please contact your admin.");
        }
    });
}

function initMedStock(){
    var dtTable = $('#myTable').DataTable({
        searching: true,
        lengthChange: true,
        pageLength: 10,
        order: [[0,'desc']],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
            { orderable: false, "targets": [1,2,3,4,5] }
        ],
        initComplete: function () {
            initFormAddStock();
            initFormDelStock();
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
}

function initFormAddStock(){
    $('table#myTable tbody').on('submit','form.addStock',function(e){
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('rooting-transfers.addStock') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                getMedStockTable(a.for);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area-addStock', a.status);
                }
                loader(false);
            }
        });
    });
}

function initFormDelStock(){
    $('table#myTable2 tbody').on('submit','form.delStock',function(e){
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('rooting-transfers.delStock') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                getMedStockTable(a.for);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area-addStock', a.status);
                }
                loader(false);
            }
        });
    });
}

$('#modalMediumStock').on('hide.bs.modal', function (e) {
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("rooting-transfers.closeModalStock") }}',
        success: function(a) {
            $('#createStep3 input[name="to_'+a.for+'"]').val(a.total);
            if(a.for == 'back'){
                $('#createStep3 input[name="leaf_count"]').val(a.leaf);
            }
        },
        error: (a) => {
            alert("Error #003, CallusTransferController-getCountMedStock function is invalid.");
        }
    });
})


</script>