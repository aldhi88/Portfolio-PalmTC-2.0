<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>
    $('#startObs').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('tc_init_id', '{{ $data["initId"] }}'); 
        $.ajax({
            type: 'POST',
            url: "{{ route('liquid-obs.store') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                if(a.status == 'success'){
                    $('#startObs input[name="action"]').val(a.data.status);
                    $('#startObs button[name="start"]').addClass('d-none');
                    $('#startObs button[name="update"]').removeClass('d-none');
                    $('#table-wrap').removeClass('d-none');
                    $('#msg-table-hide').addClass('d-none');
                    initDt(a.data.obs_date);
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

    initDt('{{ $data["date_ob"] }}');
    function initDt(obsDate = null){
        var dtTable = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            order: [ [0,'desc'] ],
            destroy: true,
            columnDefs: [
                { className: 'text-center', targets: ['_all'] },
            ],
            ajax: {
                url:'{{ route("liquid-obs.dtCreate") }}',
                data:{
                    obsId:'{{ $data["obsId"] }}',
                    initId:'{{ $data["initId"] }}',
                    obsDate:obsDate,
                }
            },
            columns: [
                { data: 'bottle_date_format', name: 'bottle_date', orderable:true, searchable:false},
                { data: 'tc_inits.tc_samples.sample_number_display', name: 'tc_inits.tc_samples.sample_number', orderable:false, searchable:true},
                { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:true},
                { data: 'alpha', name: 'alpha', orderable:true, searchable:true},
                { data: 'cycle', name: 'cycle', orderable:true, searchable:true},
                { data: 'first_total', name: 'first_total', orderable:false, searchable:false},
                { data: 'form_liquid', name: 'form_liquid', orderable:false, searchable:false},
                { data: 'form_oxidate', name: 'form_oxidate', orderable:false, searchable:false},
                { data: 'form_contam', name: 'form_contam', orderable:false, searchable:false},
                { data: 'form_other', name: 'form_other', orderable:false, searchable:false},
                { data: 'last_total', name: 'last_total', orderable:false, searchable:false},
            ],
            initComplete: function () {
                initFormObs();
                initSearchColumn();
            }
        });
    }
    
    function initFormObs(){
        $("input.form-obs").change(function(){
            var formData = {};
            formData.tc_init_id = '{{ $data["initId"] }}';
            formData.tc_liquid_ob_id = '{{ $data["obsId"] }}';
            formData.tc_liquid_bottle_id = $(this).attr('data-id');
            formData.alpha = $(this).attr('data-alpha');
            formData.cycle = $(this).attr('data-cycle');
            formData.tc_worker_id = $('#startObs select[name="tc_worker_id"]').val();
            formData.type = $(this).attr('data-type');
            formData.value = $(this).val();
            $.ajax({
                type: 'POST', cache: false, processData: true,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, //note!
                url: '{{ route("liquid-obs.storeObDetail") }}',
                data: formData,
                success: function(a) {
                    $('#alert-area2').empty();
                    $('#myTable').DataTable().ajax.reload(function(){ 
                        initFormObs();
                        initSearchColumn();
                    },false);
                    showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                },
                error: (a) => {
                    alert("Error #003, CallusObservationController - store function is invalid.");
                }
            });
            
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

</script>