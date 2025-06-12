<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        order:[],
        columnDefs: [
            { className: 'text-center', targets: [0,1,2] },
        ],
        ajax: {
            url:'{{ route("inits.dtShow") }}',
            data:{ id:'{{ $data["tc_init_id"] }}'}
        },
        columns: [
            { data: 'block_number', name: 'block_number', orderable:true, searchable:true},
            { data: 'bottle_number', name: 'bottle_number', orderable:true, searchable:true},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:true, searchable:true},
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
                dtTable
                    .column( $(this).parent().index() )
                    .search( this.value )
                    .draw();
            });
        }
    });
</script>