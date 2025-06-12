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
            url: "{{ route('rooting-transfers.dtShow') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'bottle_date_format', name: 'tc_rooting_bottles.bottle_date', orderable:true, searchable:false},
            { data: 'work_date_format', name: 'tc_rooting_obs.ob_date', orderable:true, searchable:false},
            { data: 'tc_rooting_bottles.alpha', name: 'tc_rooting_bottles.alpha', orderable:false, searchable:false},
            { data: 'bottle_rooting', name: 'bottle_rooting', orderable:false, searchable:false},
            { data: 'leaf_rooting', name: 'leaf_rooting', orderable:false, searchable:false},
            { data: 'bottle_transferred', name: 'bottle_transferred', orderable:false, searchable:false},
            { data: 'leaf_transferred', name: 'leaf_transferred', orderable:false, searchable:false},
            { data: 'bottle_back', name: 'bottle_back', orderable:false, searchable:false},
            { data: 'leaf_back', name: 'leaf_back', orderable:false, searchable:false},
            { data: 'bottle_out', name: 'bottle_out', orderable:false, searchable:false},
            { data: 'leaf_out', name: 'leaf_out', orderable:false, searchable:false},
            { data: 'bottle_left', name: 'bottle_left', orderable:false, searchable:false},
            { data: 'leaf_left', name: 'leaf_left', orderable:false, searchable:false},
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
            url: "{{ route('rooting-transfers.dtShow2') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'transfer_date_format', name: 'transfer_date', orderable:true, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:false},
            { data: 'tc_laminars.code', name: 'tc_laminars.code', orderable:false, searchable:false},
            { data: 'alpha', name: 'alpha', orderable:false, searchable:false},
            { data: 'to_root1_bottle', name: 'to_root1_bottle', orderable:false, searchable:false},
            { data: 'to_root1_leaf', name: 'to_root1_leaf', orderable:false, searchable:false},
            { data: 'to_root2', name: 'to_root2', orderable:false, searchable:false},
            { data: 'to_aclim', name: 'to_aclim', orderable:false, searchable:false},
            { data: 'action', name: 'action', orderable:false, searchable:false},
        ]
    });


$("#myTable2").on("click","button.printByTransfer", function(){
    var transferId = $(this).attr('transfer-id');
    window.open('{{ route("rooting-transfers.printByTransfer") }}?id='+transferId);
})
$("#myTable2").on("click","button.printPlant", function(){
    var transferId = $(this).attr('transfer-id');
    window.open('{{ route("rooting-transfers.printPlant") }}?id='+transferId);
})
</script>
