<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>
    $('#startObs').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('tc_harden_id', '{{ $data["tc_harden_id"] }}');
        formData.append('tc_init_id', '{{ $data["initId"] }}');
        $.ajax({
            type: 'POST',
            url: "{{ route('harden-obs.store') }}",
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

    var dateOb = '{{ $data["date_ob"] }}';
    if(dateOb!='' || dateOb){
        initDt();
    }
    function initDt(){
        var dtTable = $('#myTable').DataTable({
            destroy: true,
            processing: false,serverSide: true,scrollX: true,pageLength: 100,
            order: [[1,'asc']],
            columnDefs: [
                { className: 'text-center', targets: ['_all'] },
            ],
            ajax: {
                url: '{{ route("harden-obs.dtObs") }}',
                data: function(d){ // note !
                    d.hardenId = '{{ $data["tc_harden_id"] }}';
                    d.obsId = '{{ $data["obsId"] }}';
                }
            },
            columns: [
                { data: 'tree_date_format', name: 'tree_date_format', orderable:false, searchable:false},
                { data: 'index_number', name: 'index_number', orderable:true, searchable:true},
                { data: 'death_form', name: 'death_form', orderable:false, searchable:false},
                { data: 'transfer_form', name: 'transfer_form', orderable:false, searchable:false},
                { data: 'pre_nursery_form', name: 'pre_nursery_form', orderable:false, searchable:false},
            ],
            initComplete: function () {
                $('#header-filter2 th').each(function() {
                    var title = $(this).text();
                    var disable = $(this).attr("disable");
                    if(disable!="true"){
                        $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search2 px-1 form-control-sm text-center"/>');
                    }
                });
                $('#header-filter2').on('keyup', ".column-search2",function () {
                    dtTable.column ( $(this).parent().index() ).search( this.value ).draw();
                });
                initFormObs();
            },

        });
    }

    function initFormObs(){
        $("body").on('click','input.item', function() {
            loader(true);
            var formData = {};
            if ($(this).is(":checked")) {
                formData.isTransfer = 1;
                formData.isDeath = 0;
            }else{
                formData.isTransfer = 0;
            }
            formData.id = $(this).val();
            formData.obsId = '{{ $data["obsId"] }}';
            formData.initId = '{{ $data["initId"] }}';
            formData.pre = $('input[name="pre_nursery"].'+$(this).attr('id')).val();

            $.ajax({ //note!
                type: 'POST', cache: false, processData: true,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route("harden-obs.storeDetail") }}',
                data: formData,
                success: function(a) {
                    $('#myTable').DataTable().ajax.reload(function(){ loader(false)},false);
                    loader(false);
                },
                error: (a) => {
                    alert("Error #003, CallusObservationController - store function is invalid.");
                }
            });
        });

        $("body").on('change','input[name="pre_nursery"]', function() {
            loader(true);
            var formData = {};
            formData.isTransfer = 1;
            formData.isDeath = 0;
            formData.id = $(this).attr('data-id');
            formData.obsId = '{{ $data["obsId"] }}';
            formData.initId = '{{ $data["initId"] }}';
            formData.pre = $(this).val();

            $.ajax({ //note!
                type: 'POST', cache: false, processData: true,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route("harden-obs.storeDetail") }}',
                data: formData,
                success: function(a) {
                    $('#myTable').DataTable().ajax.reload(function(){ loader(false)},false);
                    loader(false);
                },
                error: (a) => {
                    alert("Error #003, CallusObservationController - store function is invalid.");
                }
            });
        });

        $("body table#myTable").on('change','select', function() {
            loader(true);
            var formData = {};
            formData.obsId = '{{ $data["obsId"] }}';
            formData.initId = '{{ $data["initId"] }}';
            formData.deathId = $(this).val();
            formData.id = $(this).attr('data-id');
            if ($(this).val() == 0) {
                formData.isDeath = 0;
            }else{
                formData.isDeath = 1;
                formData.isTransfer = 0;
            }
            $.ajax({ //note!
                type: 'POST', cache: false, processData: true,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route("harden-obs.storeDetail") }}',
                data: formData,
                success: function(a) {
                    $('#myTable').DataTable().ajax.reload(function(){ loader(false)},false);
                    loader(false);
                },
                error: (a) => {
                    alert("Error #003, CallusObservationController - store function is invalid.");
                }
            });

        });
    }

    $("body").on('click','input.checkbox', function() {
        loader(true);
        var formData = {};
        if ($(this).is(":checked")) {
            formData.action = 1;
        }else{
            formData.action = 0;
        }
        formData.hardenId = '{{ $data["tc_harden_id"] }}';
        formData.obsId = '{{ $data["obsId"] }}';
        formData.initId = '{{ $data["initId"] }}';

        $.ajax({ //note!
            type: 'POST', cache: false, processData: true,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route("harden-obs.storeDetailAll") }}',
            data: formData,
            success: function(a) {
                $('#myTable').DataTable().ajax.reload(function(){ loader(false)},false);
                loader(false);
            },
            error: (a) => {
                alert("Error #003, CallusObservationController - store function is invalid.");
            }
        });
    });

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
