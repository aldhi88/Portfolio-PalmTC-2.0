<script>
    $('button.resample-clear').on('click', function(){
        $('form input[name="resample"]').val('');
        $('form input[name="resample_display"]').val('');
    })
    // generate data sample
    function genSample(){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("samples/get-sample") }}',
            success: function(a) {
                $('#sampleModal .modal-content').html(a);
                initSampleDt();
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }
    function initSampleDt(){
        var dtSample = $('#myTableSample').DataTable({
            processing: true,
            serverSide: true,
            order:[
                [2, 'desc']
            ],
            columnDefs: [
                { className: 'text-right', targets: [] },
            ],
            ajax: '{!! route('samples.dtSample') !!}',
            columns: [
                { data: 'sample_number_link', name: 'sample_number', orderable:true, searchable:true},
                { data: 'master_treefile.noseleksi', name: 'master_treefile.noseleksi', orderable:true, searchable:true},
                { data: 'resample_display', name: 'created_at', orderable:true, searchable:false},
                { data: 'program', name: 'program', orderable:false, searchable:true},
            ],
            initComplete: function () {
                $('#sample-header-filter th').each(function() {
                    $(this).html('<input type="text" class="form-control column-search-sample px-1 form-control-sm"/>');
                });
                $('#sample-header-filter').on('keyup', ".column-search-sample",function () {
                    dtSample
                        .column( $(this).parent().index() )
                        .search( this.value )
                        .draw();
                });
                $('#myTableSample').on('click', 'a', function(e){
                    $('form input[name="resample"]').val($(this).attr('id'));
                    $('form input[name="resample_display"]').val($(this).attr('display'));

                    $('form input[name="master_treefile_id"]').val($(this).attr('id-treefile'));
                    $('form input[name="no_seleksi"]').val($(this).attr('noseleksi'));
                    
                    $('#sampleModal').modal('toggle');
                })
            }
        });
    }
    $("#sampleModal").on("show.bs.modal", function(e) {
        genSample();
    });

</script>