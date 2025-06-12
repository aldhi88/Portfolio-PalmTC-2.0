<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        pageLength: 25,processing: true,serverSide: true,
        order: [[0, 'desc'],],
        columnDefs: [
            { className: 'text-right', targets: [] },
        ],
        ajax: '{!! route('treefiles.dt') !!}',
        columns: [
            { data: 'id', name: 'id', orderable: true, searchable:true },
            { data: 'noseleksi', name: 'noseleksi', orderable: true, searchable:true },
            { data: 'family', name: 'family', orderable: true },
            { data: 'indukbet', name: 'indukbet', orderable: true },
            { data: 'indukjan', name: 'indukjan', orderable: true },
            { data: 'blok', name: 'blok', orderable: true },
            { data: 'baris', name: 'baris', orderable: true },
            { data: 'pokok', name: 'pokok', orderable: true },
            { data: 'tahuntanam', name: 'tahuntanam', orderable: true },
            { data: 'tipe', name: 'tipe', orderable: true },
        ],
        initComplete: function () {
            $('#header-filter th').each(function() {
                var title = $(this).text();
                var disable = $(this).attr("disable");
                if(disable!="true"){
                    $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search px-1 form-control-sm"/>');
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
</script>