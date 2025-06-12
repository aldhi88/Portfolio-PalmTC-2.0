<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    // datatables
var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [ [0,'desc'] ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("matur-obs.dt") }}',
        columns: [
            { data: 'sample_number_format', name: 'tc_samples.sample_number', orderable:true, searchable:false},
            { data: 'sum_bottle', name: 'sum_bottle', orderable:false, searchable:false},
            { data: 'obs_count', name: 'obs_count', orderable:false, searchable:false},
            { data: 'sum_bottle_matur_format', name: 'sum_bottle_matur', orderable:false, searchable:false},
            { data: 'sum_bottle_oxidate_format', name: 'sum_bottle_oxidate', orderable:false, searchable:false},
            { data: 'sum_bottle_contam_format', name: 'sum_bottle_contam', orderable:false, searchable:false},
            { data: 'sum_bottle_other_format', name: 'sum_bottle_other', orderable:false, searchable:false},
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
                    dtTable
                        .column( $(this).parent().index() )
                        .search( this.value )
                        .draw();
                });
            }
    });

    $("body").on("click", "button#btnPrint", function(){
        var div = document.getElementById("exportPrint");
        var route = '{{ route("matur-obs.printObsForm") }}';
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }
</script>