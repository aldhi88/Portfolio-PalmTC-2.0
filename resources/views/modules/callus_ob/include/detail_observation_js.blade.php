<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [1,"desc"],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: {
            url: "{{ route('callus-obs.dtDetailObs') }}",
            data: {initId:'{{ $data["initId"] }}'}
        },
        columns: [
            { data: 'action', name: 'action', orderable:false, searchable:false},
            { data: 'date_ob_format', name: 'id', orderable:true, searchable:false},
            { data: 'tc_workers.code', name: 'tc_workers_code', orderable:false, searchable:false},
            { data: 'new_bottle_callus', name: 'id', orderable:false, searchable:false},
            { data: 'old_bottle_callus', name: 'id', orderable:false, searchable:false},
            { data: 'new_explant_callus', name: 'id', orderable:false, searchable:false},
            { data: 'old_explant_callus', name: 'id', orderable:false, searchable:false},
            { data: 'bottle_oxi', name: 'id', orderable:false, searchable:false},
            { data: 'explant_oxi', name: 'id', orderable:false, searchable:false},
            { data: 'bottle_contam', name: 'id', orderable:false, searchable:false},
            { data: 'explant_contam', name: 'id', orderable:false, searchable:false},
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

    $("body").on("click", "button#btnPrint", function(){
        var initId = $(this).attr('init-id');
        var page = $('input[name="page"]').val();
        var div = document.getElementById("exportPrint");
        var route = '{{ route("callus-obs.printObsForm") }}?initId='+initId+'&page='+page;
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }
</script>