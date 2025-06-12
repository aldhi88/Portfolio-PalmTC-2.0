<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    var dtTable = $('#myTable').DataTable({
        processing: true,serverSide: true,pageLength: 25,order: [1,"desc"],
        columnDefs: [
            { className: 'text-center', targets: "_all" },
        ],
        ajax: {
            url: "{{ route('aclim-transfers.dtShow') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'tree_date_format', name: 'tree_date_format', orderable:true, searchable:false},
            { data: 'ob_date_format', name: 'ob_date', orderable:true, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:false},
            { data: 'need_transfer', name: 'need_transfer', orderable:false, searchable:false},
            { data: 'button_transfer', name: 'button_transfer', orderable:false, searchable:false},
        ]
    });

    var dtTable2 = $('#myTable2').DataTable({
        processing: true,serverSide: true,pageLength: 25,order: [3,"desc"],
        columnDefs: [
            { className: 'text-center', targets: "_all" },
        ],
        ajax: {
            url: "{{ route('aclim-transfers.dtShow2') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'transfer_date_format', name: 'transfer_date_format', orderable:false, searchable:false},
            { data: 'ob_date_format', name: 'ob_date_format', orderable:false, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:false},
            { data: 'to_self', name: 'transfer_date', orderable:true, searchable:false},
            { data: 'to_next', name: 'to_next', orderable:false, searchable:false},
            { data: 'btn_delete', name: 'btn_delete', orderable:false, searchable:false},
        ]
    });


$("#myTable2").on("click","button.printLabel", function(){
    var transferId = $(this).attr('transfer-id');
    var type = $(this).attr('data-type');
    window.open('{{ route("aclim-transfers.printLabel") }}?id='+transferId+'&type='+type);
})
</script>
