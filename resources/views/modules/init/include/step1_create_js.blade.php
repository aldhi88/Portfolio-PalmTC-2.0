<script>
    $("#sampleModal").on("show.bs.modal", function(e) {
        genSample();
    });
    function genSample(){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("samples/get-sample") }}',
            success: function(a) {
                $('#sampleModal .modal-content').html(a);
                initSampleDt();
            },
            error: (a) => {
                alert("Error #003, generate sample data in initiation form.");
            }
        });
    }
    function initSampleDt(){
        var dtSample = $('#myTableSample').DataTable({
            processing: true,
            serverSide: true,
            order:[
                [0, 'desc']
            ],
            columnDefs: [
                { className: 'text-center', targets: ['_all'] },
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
                    $('form input[name="tc_sample_id"]').val($(this).attr('id'));
                    $('form input[name="sample_number_display"]').val($(this).attr('display'));

                    $('form input[name="master_treefile_id"]').val($(this).attr('id-treefile'));
                    $('form input[name="no_seleksi"]').val($(this).attr('noseleksi'));

                    $('#sampleModal').modal('toggle');
                })
            }
        });
    }

    function clearValidationCreate(){
        $('#formStep1 input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#formStep1 span.'+key).text(value);
        $('#formStep1 input[name="'+key+'"]').addClass('is-invalid');
    }
    $('#formStep1').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('inits.submitStep1') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                genStep1();
                genStep2();
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area-step1', a.status);
                }
                loader(false);
            }
        });
    });
</script>
