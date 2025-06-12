<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    genSummary();
    function genSummary(){
        var id = $('input[name="initId"]').val();
        $.ajax({
            type: 'GET', cache: false, processData: true,
            url: '{{ route("inits.dtBottleSummary") }}',
            data: { id : id },
            success: function(a) {
                $('.table-container').html(a);
            },
            error: (a) => {
                alert("Error #003, dtBottleSummary error.");
            }
        });
    }

    function getWorkerId(){
        var workerId = $("select[name='tc_worker_id']").val();
        return workerId;
    }
    addBlockOption();
    function addBlockOption(){
        var workerId = getWorkerId();
        var id = $('input[name="initId"]').val();
        $.ajax({
            type: 'GET', cache: false, processData: true,
            url: '{{ route("inits.addBlockOption") }}',
            data: {
                worker_id:workerId,
                init_id:id,
            },
            success: function(a) {
                $("#no_block_area").html(a);
                loader(false);
            },
            error: (a) => {
                alert("Error #003, InitiationController-getStep2 function is invalid.");
            }
        });
    }
    $("select[name='tc_worker_id']").on("change",function(){
        addBlockOption();
    })

    $('#formAddBottleWorker').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('inits.formAddBottleWorker') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                dtTable.ajax.reload();
                genSummary();
                $('#formAddBottleWorker').trigger('reset');
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
                    showAlert('danger', 'times', 'alert-area', a.status);
                }
                loader(false);
            }
        });
    });

    // initDt();
    // function initDt(){
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        order:[],
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,3] },
        ],
        ajax: {
            url:'{{ route("inits.dtBottle") }}',
            data:{
                id: $('input[name="initId"]').val()
            }
        },
        columns: [
            { data: 'block_number', name: 'block_number', orderable:true, searchable:true},
            { data: 'bottle_number', name: 'bottle_number', orderable:true, searchable:true},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:true, searchable:true},
            { data: 'status_control', name: 'status_control', orderable:true, searchable:true},
        ],
        initComplete: function () {
            initSwitch();
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
    // }

    function initSwitch(){
        $("#myTable").on("click","i.switch",function(){
            loader(true);
            var status = parseInt($(this).attr('data-status'))-1;
            status = status*status;
            var id = $(this).attr('data-id');
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST', cache: false, processData: true,
                url: '{{ route("inits.changeBottleStatus") }}',
                data: {
                    status:status,
                    id:id,
                },
                success: function(a) {
                    dtTable.ajax.reload();
                    genSummary();
                    loader(false);
                },
                error: (a) => {
                    alert("Error #003, InitiationController-getStep2 function is invalid.");
                }
            });
        })
    }
</script>
