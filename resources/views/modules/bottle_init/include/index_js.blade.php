<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true, serverSide: true, pageLength: 100, lengthChange: false,
        order: [ [2, 'asc'] ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("bottle-inits.dtIndex") }}',
        columns: [
            { data: 'keyword', name: 'keyword', orderable: true, searchable:true},
            { data: 'name_custom', name: 'column_name', orderable: true, searchable:true},
            { data: 'bottle_list', name: 'order', orderable: true, searchable:true},
        ]
    });
</script>


