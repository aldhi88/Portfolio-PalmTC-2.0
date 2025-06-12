<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        scrollX: true,
        order:[
            [0, 'desc'],
        ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("inits.dt") }}',
        columns: [
            { data: 'date', name: 'id', orderable:true, searchable:false},
            { data: 'tc_samples.sample_number_display', name: 'tc_samples.sample_number', orderable:true, searchable:true},
            { data: 'tc_samples.master_treefile.noseleksi', name: 'tc_samples.master_treefile.noseleksi', orderable:true, searchable:true},
            { data: 'tc_samples.master_treefile.tahuntanam', name: 'tc_samples.master_treefile.tahuntanam', orderable:true, searchable:true},
            { data: 'tc_samples.master_treefile.tipe', name: 'tc_samples.master_treefile.tipe', orderable:true, searchable:true},
            { data: 'tc_samples.program', name: 'tc_samples.program', orderable:true, searchable:true},
            { data: 'number_of_block', name: 'number_of_block', orderable:false, searchable:false},
            { data: 'action_bottle', name: 'total_bottle', orderable:true, searchable:true},
            { data: 'total_explant', name: 'total_explant', orderable:false, searchable:false},
            { data: 'tc_rooms.code', name: 'tc_rooms.code', orderable:true, searchable:true},
            { data: 'status_stop', name: 'status_stop', orderable:true, searchable:true},
            { data: 'id', name: 'id', orderable:true, searchable:false},
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
