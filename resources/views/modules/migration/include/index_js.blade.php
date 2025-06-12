<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,serverSide: true,pageLength: 100,
        order: [[1, 'asc'],[2, 'asc']],
        columnDefs: [
            { className: 'text-center', targets: [] },
        ],
        ajax: '{{ route("migrations.dtIndex") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable:false },
            { data: 'migration', name: 'migration', orderable: true, searchable:true },
            { data: 'batch_format', name: 'batch', orderable: true, searchable:false },
        ],
        initComplete: function () {
            delProcess();
        }
    });

    function delProcess(){
        $('body').on('change','input.batch',function(){
            var formData = {};
            formData.id = $(this).attr('data-id');
            formData.value = $(this).val();
            $.ajax({
                type: 'POST', cache: false, processData: true,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route("migrations.update") }}',
                data: formData,
                success: function(a) {
                    $('#myTable').DataTable().ajax.reload(function(){
                        // initFormObs();
                        // initSearchColumn();
                    },false);
                    // showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                },
                error: (a) => {
                    // alert("Error #003, CallusObservationController - store function is invalid.");
                }
            });

        });
    }
</script>
