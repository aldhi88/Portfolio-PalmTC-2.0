<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [1,"desc"],
        columnDefs: [
            { className: 'text-center', targets: "_all" },
        ],
        ajax: {
            url: "{{ route('callus-transfers.dtDetailTransfer') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'rownum', name: 'rownum', orderable:false, searchable:false},
            { data: 'date_obs_format', name: 'date_ob', orderable:true, searchable:false},
            { data: 'bottle_callus', name: 'bottle_callus', orderable:false, searchable:false},
            { data: 'transfered', name: 'transfered', orderable:false, searchable:false},
            { data: 'bottle_left', name: 'bottle_left', orderable:false, searchable:false},
            { data: 'transfer', name: 'transfer', orderable:false, searchable:false},
        ],
        initComplete: function () {
            // initSearchColumn();
        }
    });

    function initSearchColumn(){
        $('#header-filter th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control text-center column-search px-1 form-control-sm"/>');
            }
        });
        $('#header-filter').on('keyup', ".column-search",function () {
            dtTable.column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    }

    var dtTable2 = $('#myTable2').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        pageLength: 100,
        order:[[1,'desc'],[0,'desc']],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: {
            url: "{{ route('callus-transfers.dtListTransferPerInit') }}",
            data: {id:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'date_work_format', name: 'id', orderable: true, searchable:false},
            { data: 'date_ob_format', name: 'date_ob_format', orderable: true, searchable:true},
            { data: 'subculture', name: 'subculture', orderable: false, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers.code', orderable: true, searchable:true},
            { data: 'tc_laminars.code', name: 'tc_laminars.code', orderable: false, searchable:false},
            { data: 'bottle_used', name: 'bottle_used', orderable: false, searchable:false},
            { data: 'new_bottle', name: 'new_bottle', orderable: false, searchable:false},
            { data: 'time_work', name: 'time_work', orderable: false, searchable:false},
            // { data: 'index_number', name: 'id', orderable: true, searchable:false},
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
                dtTable2
                    .column( $(this).parent().index() )
                    .search( this.value )
                    .draw();
            });
        }
    });

    $("body").on("click", "button#btnPrint", function(){
        var page = $('input[name="page"]').val();
        var div = document.getElementById("exportPrint");
        var route = '{{ route("callus-transfers.printBlankForm") }}?&page='+page;
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }
</script>