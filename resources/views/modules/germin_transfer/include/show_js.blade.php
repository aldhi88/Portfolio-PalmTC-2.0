<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [0,"desc"],
        columnDefs: [
            { className: 'text-center', targets: "_all" },
        ],
        ajax: {
            url: "{{ route('germin-transfers.dtShow') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'bottle_date_format', name: 'tc_germin_bottles.bottle_date', orderable:true, searchable:false},
            { data: 'work_date_format', name: 'tc_germin_obs.work_date', orderable:true, searchable:false},
            { data: 'tc_germin_bottles.alpha', name: 'tc_germin_bottles.alpha', orderable:false, searchable:false},
            { data: 'bottle_germin', name: 'bottle_germin', orderable:false, searchable:false},
            { data: 'transferred', name: 'transferred', orderable:false, searchable:false},
            { data: 'bottle_back', name: 'bottle_back', orderable:false, searchable:false},
            { data: 'bottle_out', name: 'bottle_out', orderable:false, searchable:false},
            { data: 'bottle_left', name: 'bottle_left', orderable:false, searchable:false},
        ]
    });


    var dtTable2 = $('#myTable2').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [0,"desc"],
        columnDefs: [
            { className: 'text-center', targets: "_all" },
        ],
        ajax: {
            url: "{{ route('germin-transfers.dtShow2') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'transfer_date_format', name: 'transfer_date', orderable:true, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:false},
            { data: 'tc_laminars.code', name: 'tc_laminars.code', orderable:false, searchable:false},
            { data: 'alpha', name: 'alpha', orderable:false, searchable:false},
            { data: 'to_self', name: 'to_self', orderable:false, searchable:false},
            { data: 'to_root', name: 'to_root', orderable:false, searchable:false},
            { data: 'action', name: 'action', orderable:false, searchable:false},
        ]
    });


$("#myTable2").on("click","button.printByTransfer", function(){
    var transferId = $(this).attr('transfer-id');
    window.open('{{ route("germin-transfers.printByTransfer") }}?id='+transferId);
})
</script>
