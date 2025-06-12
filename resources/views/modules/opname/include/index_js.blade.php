<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        // language: {
        //     paginate: { previous: "<i class='mdi mdi-chevron-left'>", next: "<i class='mdi mdi-chevron-right'>" }
        // },
        // drawCallback: function () {
        //     $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        // },
        columnDefs: [
            { className: 'text-right', targets: [] },
        ],
        ajax: '{!! route('opname.dt') !!}',
        columns: [
            // { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'id', name: 'id', orderable: false },
            { data: 'custom_name', name: 'custom_name', orderable: false },
            { data: 'custom_created_at', name: 'custom_created_at', orderable: false },
            { data: 'tc_medias.tc_worker_id', name: 'tc_medias.tc_worker_id', orderable: false },
            { data: 'stock_in', name: 'stock_in', orderable: false },
            { data: 'stock_out', name: 'stock_out', orderable: false },
            // { data: 'action', name: 'action', searchable: false, orderable: false },
        ]
    });
</script>