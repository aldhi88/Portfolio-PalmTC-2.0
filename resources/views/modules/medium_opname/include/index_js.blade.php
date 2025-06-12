<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        order: [
            [0, 'desc']
        ],
        pageLength: 50,
        columnDefs: [
            { className: 'text-right', targets: [3,4] },
        ],
        ajax: '{!! route('medium-validate.dt') !!}',
        columns: [
            { data: 'custom_created_at', name: 'created_at', orderable: true, searchable:true },
            { data: 'tc_medium_stocks.created_at_long_format', name: 'id', orderable: true, searchable:true },
            { data: 'medium_name', name: 'medium_name', orderable: true, searchable:true },
            { data: 'stock_in', name: 'stock_in', orderable: true, searchable:true },
            { data: 'stock_out', name: 'stock_out', orderable: true, searchable:true },
            { data: 'desc', name: 'desc', orderable: true, searchable:true },
        ]
    });
</script>