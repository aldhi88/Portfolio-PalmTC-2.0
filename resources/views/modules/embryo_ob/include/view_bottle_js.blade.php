<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        order:[[1,'asc']],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: {
            url: "{{ route('embryo-obs.dtListBottle') }}",
            data: {id:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'date_work_format', name: 'date_work', orderable: true, searchable:false},
            { data: 'tc_init_id', name: 'tc_init_id', orderable: true, searchable:true},
            { data: 'tc_worker_id', name: 'tc_worker_id', orderable: false, searchable:true},
            { data: 'sub', name: 'sub', orderable: false, searchable:false},
            { data: 'sub', name: 'sub', orderable: false, searchable:false},
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
</script>