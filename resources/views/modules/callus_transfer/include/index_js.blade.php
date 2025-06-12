<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>

    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 25,
        order: [
            [0,'desc']
        ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: "{{ route('callus-transfers.dt') }}",
        columns: [
            { data: 'sample_action', name: 'tc_samples.sample_number', orderable:true, searchable:true},
            { data: 'date', name: 'date', orderable:false, searchable:false},
            { data: 'total_ob', name: 'id', orderable:false, searchable:false},
            { data: 'total_callus', name: 'id', orderable:false, searchable:false},
            { data: 'transferred', name: 'id', orderable:false, searchable:false},
            { data: 'bottle_left', name: 'id', orderable:false, searchable:false},
            
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