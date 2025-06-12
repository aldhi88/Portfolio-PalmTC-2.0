<script>
    // generate data treefile
    function genTreefile(){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("samples/get-treefile") }}',
            success: function(a) {
                $('#treefileModal .modal-content').html(a);
                initDt();
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
    function initDt(){
        var dtTable = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            order:[[1,'asc']],
            columnDefs: [
                { className: 'text-center', targets: ['_all'] },
            ],
            ajax: '{!! route('samples.dtTreefile') !!}',
            columns: [
                { data: 'status', name: 'status', orderable:false, searchable:false},
                { data: 'noseleksi_link', name: 'noseleksi'},
                { data: 'family', name: 'family'},
                { data: 'indukbet', name: 'indukbet'},
                { data: 'indukjan', name: 'indukjan'},
                { data: 'blok', name: 'blok'},
                { data: 'baris', name: 'baris'},
                { data: 'pokok', name: 'pokok'},
                { data: 'tahuntanam', name: 'tahuntanam'},
                { data: 'tipe', name: 'tipe', orderable: true },
            ],
            initComplete: function () {
                $('#header-filter th').each(function() {
                    $(this).html('<input type="text" class="form-control column-search px-1 form-control-sm"/>');
                });
                $('#header-filter').on('keyup', ".column-search",function () {
                    dtTable
                        .column( $(this).parent().index() )
                        .search( this.value )
                        .draw();
                });

                $('#myTable').on('click', 'a', function(e){
                    $('form input[name="master_treefile_id"]').val($(this).attr('id'));
                    $('form input[name="no_seleksi"]').val($(this).attr('noseleksi'));
                    $('#treefileModal').modal('toggle');
                })
            }
        });
    }
    $("#treefileModal").on("show.bs.modal", function(e) {
        genTreefile();
    });
</script>