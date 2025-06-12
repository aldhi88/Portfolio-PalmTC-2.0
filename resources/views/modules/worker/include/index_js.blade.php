<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,serverSide: true,pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("workers.dt") }}',
        columns: [
            { data: 'created_at_custom', name: 'created_at', orderable: true, searchable:false },
            { data: 'custom_no_pekerja', name: 'no_pekerja', orderable: true, searchable:true },
            { data: 'name', name: 'name', orderable: true, searchable:true },
            { data: 'date_of_birth_format', name: 'date_of_birth_format', orderable: false, searchable:true },
            { data: 'code', name: 'code', orderable: false, searchable:true },
            { data: 'custom_status', name: 'status', orderable: true, searchable:false },
        ],
        initComplete: function(settings){
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
