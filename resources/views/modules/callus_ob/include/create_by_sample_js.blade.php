<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>
    initDt();
    function initDt(){
        var dtTable = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            // stateSave: true,
            order: [ [1,'asc'] ],
            destroy: true,
            columnDefs: [
                { className: 'text-center', targets: [0,1,2] },
            ],
            ajax: {
                url:'{{ route("callus-obs.dtBottle") }}',
                data:{
                    initId: '{{ $data["init"]->id }}',
                    obsId: '{{ $data["obsId"] }}',
                }
            },
            columns: [
                { data: 'block_number', name: 'block_number', orderable:true, searchable:true},
                { data: 'bottle_number', name: 'bottle_number', orderable:true, searchable:true},
                { data: 'worker_code', name: 'worker_code', orderable:true, searchable:true},
                { data: 'explant_number', name: 'form_search', orderable:false, searchable:true},
            ],
            initComplete: function () {
                initSearchColumn();
            }
        });
    }
    function initSearchColumn(){
        $('#header-filter th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control text-center column-search px-1 form-control-sm"/>');
            }
        });
        $('#header-filter').on('keyup', ".column-search",function () {
            $('#myTable').DataTable().column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    }

    $('#startObs').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('callus-obs.startObs') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                if(a.status == 'success'){
                    $('#startObs input[name="action"]').val(a.data.status);
                    $('#startObs button[name="start"]').addClass('d-none');
                    $('#startObs button[name="update"]').removeClass('d-none');
                    $('#table-wrap').removeClass('d-none');
                    $('#msg-table-hide').addClass('d-none');
                    initDt();
                }
                loader(false);
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
            },
            error: (a) => {
                showAlert('danger', 'times', 'alert-area', a.status);
                loader(false);
            }
        });
    });

    $("table#myTable").on('click','input:checkbox', function() {
        loader(true);
        var formData = {};
        formData.result = $(this).val();
        formData.explant_number = $(this).attr('data-explant');
        formData.tc_callus_ob_id = $('input[name="tc_callus_ob_id"]').val();
        formData.tc_init_id = $('input[name="tc_init_id"]').val();
        formData.tc_init_bottle_id = $(this).attr('data-bottle');
        if ($(this).is(":checked")) {
            formData.action = 'insert';
        }else{
            formData.action = 'delete';
        }
        $.ajax({ //note!
            type: 'POST', cache: false, processData: true,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route("callus-obs.store") }}',
            data: formData,
            success: function(a) {
                $('#myTable').DataTable().ajax.reload(function(){ loader(false)},false);
            },
            error: (a) => {
                alert("Error #003, CallusObservationController - store function is invalid.");
            }
        });
    });

    $("table#myTable").on('change','select', function() {
        loader(true);
        var formData = {};
        formData.result = 3;
        formData.tc_callus_ob_id = $('input[name="tc_callus_ob_id"]').val();
        formData.tc_init_id = $('input[name="tc_init_id"]').val();
        formData.tc_init_bottle_id = $(this).attr('data-bottle');
        formData.tc_contamination_id = $(this).val();
        if(formData.tc_contamination_id == 0){
            formData.action = 'delete';
        }else{
            formData.action = 'insert';
        }

        $.ajax({
            type: 'POST', cache: false, processData: true,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route("callus-obs.store") }}',
            data: formData,
            success: function(a) {
                $('#myTable').DataTable().ajax.reload(function(){ loader(false)},false);
            },
            error: (a) => {
                alert("Error #003, CallusObservationController - store function is invalid.");
            }
        });

    });
</script>
