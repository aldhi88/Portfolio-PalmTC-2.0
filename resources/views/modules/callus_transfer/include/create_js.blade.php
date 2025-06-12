<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
var dtTable = $('#myTable').DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    pageLength: 100,
    order:[[0,'desc']],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url: "{{ route('callus-transfers.dtCallusTransfer') }}",
        data: {id:'{{ $data["obsId"] }}'}
    },
    columns: [
        { data: 'date_work_format', name: 'date_work', orderable: true, searchable:false},
        { data: 'tc_workers.code', name: 'tc_workers.code', orderable: true, searchable:true},
        { data: 'tc_laminars.code', name: 'tc_laminars.code', orderable: false, searchable:false},
        { data: 'bottle_used', name: 'bottle_used', orderable: false, searchable:false},
        { data: 'new_bottle', name: 'new_bottle', orderable: false, searchable:false},
        { data: 'time_work', name: 'id', orderable: false, searchable:false},
        { data: 'delete', name: 'delete', orderable: false, searchable:false},
    ],
    initComplete: function () {
        $('#header-filter th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search px-1 form-control-sm"/>');
            }
        });
        $('#header-filter').on('keyup', ".column-search",function () {
            dtTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    }
});

var dtTable2 = $('#myTableMediumStock').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    order:[[0,'desc']],
    columnDefs: [
        { className: 'text-right', targets: [4] },
        { className: 'text-center', targets: ["_all"] },
    ],
    ajax: '{!! route('callus-transfers.dtPickMedStock') !!}',
    columns: [
        { data: 'created_at_short_format', name: 'created_at', orderable: true, searchable:false},
        { data: 'tc_mediums_code', name: 'tc_mediums_code', orderable: true, searchable:true},
        { data: 'tc_bottles_code', name: 'tc_bottles_code', orderable: true, searchable:true},
        { data: 'tc_agars_code', name: 'tc_agars_code', orderable: true, searchable:true},
        { data: 'current_stock', name: 'current_stock', orderable: false, searchable:false},
        { data: 'form', name: 'id', orderable: false, searchable:false},
    ],
    initComplete: function () {
        initAddStockForm();
        $('#myTableMediumStockFilter th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search px-1 form-control-sm"/>');
            }
        });
        $('#myTableMediumStockFilter').on('keyup', ".column-search",function () {
            dtTable2
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    }
});

var dtTable3 = $('#mediumStockPicked').DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    destroy: true,
    pageLength: 100,
    order:[[0,'desc']],
    columnDefs: [
        { className: 'text-right', targets: [4] },
        { className: 'text-center', targets: ["_all"] },
    ],
    ajax: '{!! route('callus-transfers.dtPickedMedStock') !!}',
    columns: [
        { data: 'created_at_short_format', name: 'created_at_short_format', orderable: true, searchable:false},
        { data: 'tc_mediums_code', name: 'tc_mediums_code', orderable: false, searchable:false},
        { data: 'tc_bottles_code', name: 'tc_bottles_code', orderable: false, searchable:false},
        { data: 'tc_agars_code', name: 'tc_agars_code', orderable: false, searchable:false},
        { data: 'stock_used', name: 'stock_used', orderable: false, searchable:false},
        { data: 'delete', name: 'delete', orderable: false, searchable:false},
    ],
    initComplete: function(){
        deletePickedMedStock();
    }
});

function clearValidationCreate(){
    $('#createCallusTransfer input').removeClass('is-invalid');
    $('span.msg').text('');
}
function cekValidationCreate(key, value){
    $('#createCallusTransfer span.'+key).text(value);
    $('#createCallusTransfer input[name="'+key+'"]').addClass('is-invalid');
}
function resetFormCreate(){
    $('#createCallusTransfer').trigger('reset');
}

getDateList();
function getDateList(){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("callus-transfers.getDateList") }}',
        data: {obsId:'{{ $data["obsId"] }}'},
        success: function(a) {
            $('#dateList').html(a);
        },
        error: (a) => {
            alert("Error #003, CallusTransferController-getDateList function is invalid.");
        }
    });
}

function setBottleLeft(){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("callus-transfers.setBottleLeft") }}',
        data: {id:'{{ $data["obsId"] }}'},
        success: function(a) {
            $('#bottleLeft').text(a.data.left);
            $('#createCallusTransfer input[name="bottle_used"]').attr('max',a.data.left);
            $('button#submitFromTransfer').prop('disabled',false);
            if(a.data.left == 0){
                $('button#submitFromTransfer').prop('disabled',true);
            }
        },
        error: (a) => {
            alert("Error #003, CallusTransferController-getCountMedStock function is invalid.");
        }
    });
}

$('#createCallusTransfer').submit(function (e) {
    loader(true);
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ route('callus-transfers.store') }}",
        data: formData,
        cache: false, contentType: false, processData: false,
        success: (a) => {
            dtTable.ajax.reload(null,false);
            dtTable3.ajax.reload();
            showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
            resetFormCreate();
            setBottleLeft();
            getDateList();
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
                $('#createModal').modal('toggle');
            }
            loader(false);
        }
    });
});

function initAddStockForm(){
    $('table#myTableMediumStock tbody').on('submit','form.addStock',function(e){
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('callus-transfers.storeStock') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                dtTable2.ajax.reload(null,false);
                dtTable3.ajax.reload(null,false);
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

function deletePickedMedStock(){
    $('table#mediumStockPicked tbody').on('submit','form.deletePickedMedStock',function(e){
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('callus-transfers.deletePickedMedStock') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                dtTable2.ajax.reload(null,false);
                dtTable3.ajax.reload(null,false);
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
        url: '{{ route("callus-transfers.getCountMedStock") }}',
        success: function(a) {
            $('input[name="new_bottle"]').val(a.data.count);
        },
        error: (a) => {
            alert("Error #003, CallusTransferController-getCountMedStock function is invalid.");
        }
    });
})

$('table#myTable tbody').on('submit','form.delTransfer',function(e){
    loader(true);
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ route('callus-transfers.delTransfer') }}",
        data: formData,
        cache: false, contentType: false, processData: false,
        success: (a) => {
            setBottleLeft();
            dtTable.ajax.reload(null,false);
            getDateList();
            showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
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

// print
$("body").on("click","button#printByGroup", function(){
    var dateWork = $('#dateList').val();
    window.open('{{ route("callus-transfers.printByGroup") }}?dateWork='+dateWork);
})

$("#myTable").on("click","button.printByTransfer", function(){
    var transferId = $(this).attr('transfer-id');
    window.open('{{ route("callus-transfers.printByTransfer") }}?transferId='+transferId);
})



</script>
