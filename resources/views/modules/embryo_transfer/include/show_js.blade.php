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
            url: "{{ route('embryo-transfers.dtShow') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'bottle_date_format', name: 'tc_embryo_bottles.bottle_date', orderable:true, searchable:false},
            { data: 'work_date_format', name: 'tc_embryo_obs.work_date', orderable:true, searchable:false},
            { data: 'tc_embryo_bottles.sub', name: 'tc_embryo_bottles.sub', orderable:false, searchable:false},
            { data: 'bottle_embryo', name: 'bottle_embryo', orderable:false, searchable:false},
            { data: 'sum_total_work', name: 'sum_total_work', orderable:false, searchable:false},
            { data: 'sum_bottle_back', name: 'sum_bottle_back', orderable:false, searchable:false},
            { data: 'transferred', name: 'transferred', orderable:false, searchable:false},
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
            url: "{{ route('embryo-transfers.dtShow2') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'transfer_date_format', name: 'transfer_date', orderable:true, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:false},
            { data: 'tc_laminars.code', name: 'tc_laminars.code', orderable:false, searchable:false},
            { data: 'to_callus', name: 'to_callus', orderable:false, searchable:false},
            { data: 'to_solid', name: 'to_solid', orderable:false, searchable:false},
            { data: 'to_suspen', name: 'to_suspen', orderable:false, searchable:false},
            { data: 'action', name: 'action', orderable:false, searchable:false},
        ]
    });


    $("#myTable2").on("click","button.printByTransfer", function(){
        var transferId = $(this).attr('transfer-id');
        window.open('{{ route("embryo-transfers.printByTransfer") }}?id='+transferId);
    })

    $("body").on("click", "button#btnPrint", function(){
        var page = $('input[name="page"]').val();
        var div = document.getElementById("exportPrint");
        var route = '{{ route("embryo-transfers.printBlankForm") }}?&page='+page;
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })
    $("body").on("click", "button#btnPrint2", function(){
        var page = $('input[name="page"]').val();
        var div = document.getElementById("exportPrint");
        var route = '{{ route("embryo-transfers.printBlankForm2") }}?&page='+page;
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }
</script>
