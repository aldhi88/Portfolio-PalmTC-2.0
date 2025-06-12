<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
        var dtTable = $('#myTable').DataTable({
            processing: true,serverSide: true,pageLength: 25,
            order: [ [0, 'desc'] ],
            columnDefs: [
                { className: 'text-center', targets: ['_all'] },
                { className: 'text-center', targets: ['_all'] },
            ],
            ajax: {
                url: '{{ route("medium-stocks.dt") }}',
                data: function(d){ // note !
                    d.id = $('select[name="filter"] option:selected').val();
                }
            },
            columns: [
                // { data: 'custom_id', name: 'id', orderable: true, searchable: true },
                { data: 'custom_name', name: 'id', orderable: true, searchable: true },
                { data: 'created_at_long_format', name: 'created_at', orderable: true, searchable: false },
                { data: 'composition', name: 'composition', orderable: false, searchable: false },
                { data: 'tc_workers.code', name: 'tc_workers.code', orderable: false, searchable: true },
                { data: 'age', name: 'created_at', orderable: true, searchable: false },
                { data: 'stock', name: 'stock', orderable: false, searchable: false },
                { data: 'current_stock', name: 'current_stock', orderable: false, searchable: false },
            ]
        });

    $('select[name="filter"]').change(function(){
        dtTable.ajax.reload();
    });


</script>

@include('modules.medium_opname.include.create_js');
