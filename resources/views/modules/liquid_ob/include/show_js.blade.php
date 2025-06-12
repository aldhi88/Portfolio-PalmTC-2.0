<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [ [0,'desc'] ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: {
            url : '{{ route("liquid-obs.dtShow") }}',
            data : {
                initId:'{{ $data["initId"] }}'
            },
        },
        columns: [
            { data: 'ob_date_format', name: 'ob_date', orderable:true, searchable:false},
            { data: 'alpha', name: 'alpha', orderable:true, searchable:true},
            { data: 'cycle', name: 'cycle', orderable:true, searchable:true},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:true, searchable:false},
            { data: 'total_bottle_liquid', name: 'total_bottle_liquid', orderable:true, searchable:true},
            { data: 'total_bottle_oxidate', name: 'total_bottle_oxidate', orderable:true, searchable:true},
            { data: 'total_bottle_contam', name: 'total_bottle_contam', orderable:true, searchable:true},
            { data: 'total_bottle_other', name: 'total_bottle_other', orderable:true, searchable:true},
        ],
        initComplete: function () {
            $('#header-filter th').each(function() {
                var title = $(this).text();
                var disable = $(this).attr("disable");
                if(disable!="true"){
                    $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search px-1 form-control-sm text-center"/>');
                }
            });
            $('#header-filter').on('keyup', ".column-search",function () {
                dtTable.column( $(this).parent().index() )
                    .search( this.value )
                    .draw();
            });
            initDelEvent();
        }
    });
    function initDelEvent(){
        $('#delModal').on('show.bs.modal', function(e) {
            $(this).find('#attr').text($(e.relatedTarget).data('attr'));
            $(this).find('input[name="id"]').val($(e.relatedTarget).data('id'));
        });
    }
    $('#formDel').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: '{{ url("liquid-obs") }}/'+formData.get('id'),
            data: formData,
            success: (a) => {
                dtTable.ajax.reload();
                $('#delModal').modal('toggle');
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
                    $('#deleteInitModal').modal('toggle');
                }
                loader(false);
            }
        });
    });
    var dtTable2 = $('#myTable2').DataTable({
        processing: true,serverSide: true,
        scrollX: true,
        pageLength: 25,
        order: [ [13,'desc'] ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: {
            url : '{{ route("liquid-obs.dtShow2") }}',
            data : {
                initId:'{{ $data["initId"] }}'
            },
        },
        columns: [
            { data: 'tc_inits.tc_samples.program', name: 'tc_inits.tc_samples.program', orderable:true, searchable:true},
            { data: 'tc_inits.tc_samples.sample_number_display', name: 'tc_inits.tc_samples.sample_number', orderable:true, searchable:true},
            { data: 'bottle_date_format', name: 'tc_liquid_bottles.bottle_date', orderable:true, searchable:true},
            { data: 'tc_liquid_bottles.alpha', name: 'tc_liquid_bottles.alpha', orderable:false, searchable:true},
            { data: 'tc_liquid_bottles.cycle', name: 'tc_liquid_bottles.cycle', orderable:false, searchable:true},
            { data: 'tc_liquid_bottles.tc_workers.code', name: 'tc_liquid_bottles.tc_workers.code', orderable:false, searchable:true},
            { data: 'first_total', name: 'first_total', orderable:false, searchable:false},
            { data: 'ob_date_format', name: 'ob_date_format', orderable:false, searchable:true},
            { data: 'tc_liquid_obs.tc_workers.code', name: 'tc_liquid_obs.tc_workers.code', orderable:false, searchable:true},
            { data: 'bottle_liquid', name: 'bottle_liquid', orderable:true, searchable:false},
            { data: 'bottle_oxidate', name: 'bottle_oxidate', orderable:false, searchable:false},
            { data: 'bottle_contam', name: 'bottle_contam', orderable:false, searchable:false},
            { data: 'bottle_other', name: 'bottle_other', orderable:false, searchable:false},
            { data: 'last_total', name: 'tc_liquid_obs.ob_date', orderable:true, searchable:false},
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
                dtTable2.column( $(this).parent().index() )
                    .search( this.value )
                    .draw();
            });
        }
    });

</script>