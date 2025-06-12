<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>

var dtTable = $('#myTable').DataTable({
        processing: true,serverSide: true,scrollX: true,pageLength: 25,
        order: [
            [0, 'desc'],
        ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("nur-lists.dt") }}',
        columns: [
            { data: 'sample_number_format', name: 'tc_samples.sample_number', orderable:true, searchable:true},
            { data: 'tc_samples.program', name: 'tc_samples.program', orderable:false, searchable:true},
            { data: 'total_date', name: 'total_date', orderable:false, searchable:false},
            { data: 'total_data', name: 'total_data', orderable:false, searchable:false},
            { data: 'total_death', name: 'total_death', orderable:false, searchable:false},
            { data: 'total_transfer', name: 'total_transfer', orderable:false, searchable:false},
            { data: 'total_active_nursery', name: 'total_active_nursery', orderable:false, searchable:false},
            { data: 'total_active_estate', name: 'total_active_estate', orderable:false, searchable:false},
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

$('#formImportModal').submit(function(e) {
    loader(true);
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ route('import.nurImport') }}",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: (a) => {
            $('#formImportModal').trigger('reset')
            if (a.status == 'success') {
                dtTable.ajax.reload();
            }
            $('#importModal').modal('toggle');
            showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
            loader(false);
        },
        error: (a) => {
            showAlert('danger', 'times', 'alert-area', a.status);
            loader(false);
        }
    });
});

</script>
