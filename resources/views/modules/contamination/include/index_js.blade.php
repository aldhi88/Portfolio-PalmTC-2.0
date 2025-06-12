<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { className: 'text-right', targets: [] },
        ],
        ajax: '{!! route('contaminations.dt') !!}',
        columns: [
            { data: 'custom_name', name: 'created_at', orderable: true, searchable:true },
            { data: 'code', name: 'code', orderable: true, searchable:true },
        ]
    });
</script>