<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    
    $("#printByBottleNumber").on("click","button#printByBottleNumberBtn", function(){
        if(!checkDateSchedule('alert-area-a')){
            return false;
        }else{
            var fromNumber = parseInt($("#printByBottleNumber input[name='from_number']").val());
            var toNumber = parseInt($("#printByBottleNumber input[name='to_number']").val());
            var initId = $("input[name='tc_init_id']").val();
    
            if(fromNumber > toNumber || fromNumber==0 || toNumber==0){
                showAlert('danger', 'times', 'alert-area-printByBottleNumber', "Error, invalid range number.");
                return false;
            }
            window.open('{{ route("callus-transfers.printByBottleNumber") }}?init='+initId+'&from='+fromNumber+'&to='+toNumber);
        }
    })

    $("#printByWorker").on("click","button#printByWorkerBtn", function(){
        if(!checkDateSchedule('alert-area-a')){
            return false;
        }else{
            var initId = $("input[name='tc_init_id']").val();
            var workerId = $("#printByWorker select[name='tc_worker_id']").val();
            window.open('{{ route("callus-transfers.printByWorker") }}?init='+initId+'&worker='+workerId);
        }
    })

    var dtTable = $('#myTablex').DataTable({
        processing: true,
        serverSide: true,
        // pageLength: 50,
        scrollX: true,
        pageLength: 20,
        lengthChange: false,
        order:[],
        columnDefs: [
            { className: 'text-center', targets: [3] },
        ],
        ajax: {
            url:'{!! route('callus-transfers.dtPrintByCheck') !!}',
            data:{
                id:'{{ 0 }}'
            }
        },
        columns: [
            { data: 'tc_init_of_blocks.block_number', name: 'tc_init_of_blocks.block_number', orderable:true, searchable:true},
            { data: 'bottle_index', name: 'bottle_index', orderable:true, searchable:true},
            { data: 'tc_init_of_blocks.tc_workers.code', name: 'tc_init_of_blocks.tc_workers.code', orderable:true, searchable:true},
            { data: 'actionColumn', name: 'actionColumn', orderable:true, searchable:true},
        ],
        initComplete: function () {
            checkBottlePrint();
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

    function checkBottlePrint(){
        $("body").on("click","input.check-bottle",function(){
            loader(true);
            if($(this).prop("checked") == true){
                var status = 1;
            }else if($(this).prop("checked") == false){
                var status = 0;
            }
            var bottleId = $(this).val();
            ajaxCheckBottlePrint(status,bottleId);
        });
    }

    function ajaxCheckBottlePrint(status,bottleId){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route('callus-transfers.checkBottlePrint') }}',
            data: {
                status:status,
                bottleId:bottleId
            },
            success: function(a) {
                dataPrintCustom(true);
                loader(false);
            },
            error: (a) => {
                alert("Error #003, generate sample data in initiation form.");
            }
        });
    }

    dataPrintCustom();
    function dataPrintCustom(reload=false){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route('callus-transfers.dataPrintCustom') }}',
            data: {},
            success: function(a) {
                $("#dataPrintCustom").html(a);
                triggerUncheck();
                if(reload){
                    dtTable.ajax.reload( null, false );
                }
            },
            error: (a) => {
                alert("Error #003, generate sample data in initiation form.");
            }
        });
    }

    function triggerUncheck(){
        $("button.trigger-check").on("click",function(){
            var bottleId = $(this).attr("value");
            ajaxCheckBottlePrint(0,bottleId);
        })
    }

    triggerUncheckAll();
    function triggerUncheckAll(){
        $("button.trigger-uncheck-all").on("click",function(){
            loader(true);
            $.ajax({
                type: 'GET', cache: false, contentType: false, processData: true,
                url: '{{ route('callus-transfers.dataPrintCustomUncheckAll') }}',
                data: {},
                success: function(a) {
                    dataPrintCustom(true);
                    loader(false);
                },
                error: (a) => {
                    alert("Error #003, generate sample data in initiation form.");
                }
            });
        })
    }

    triggerPrintCheckBtn();
    function triggerPrintCheckBtn(){
        $("button.trigger-print-check").on("click",function(){
            checkBeforePrintCheck();
        })
    }
    function triggerPrintCheck(){
        if(!checkDateSchedule('alert-area-c')){
            return false;
        }else{
            var initId = $("input[name='tc_init_id']").val();
            window.open('{{ route("callus-transfers.triggerPrintCheck") }}?init='+initId);
        }
    }

    function checkBeforePrintCheck(){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ url("initiations/checkBeforePrintCheck") }}',
            data: {},
            success: function(a) {
                if(a.status == 'error'){
                    showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                    return false;
                }else{
                    triggerPrintCheck();
                }
            },
            error: (a) => {
                alert("Error #003, generate sample data in initiation form.");
            }
        });
    }
    
</script>