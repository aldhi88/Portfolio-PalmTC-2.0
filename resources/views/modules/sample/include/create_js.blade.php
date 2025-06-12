<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/moment.js/moment.min.js')}}"></script>

@include('modules.sample.include.treefile_js')
{{-- @include('modules.sample.include.sample_js') --}}
<script>
    genSampleNumb();
    function genSampleNumb(){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("samples/get-sample-number") }}',
            success: (a) => {
                $('#formCreateModal input[name="display_number"]').val(a.data.data['display_number']);
                $('#formCreateModal input[name="sample_number"]').val(a.data.data['sample_number']);
            },
            error: (a) => {
                alert("Error #003, please contact your admin.");
            }
        });
    }

    getWeek();
    function getWeek(){
        var date = $('#formCreateModal input[name="created_at"]').val();
        var weekNumb = moment(date, "YYYYMMDD").week();
        $('#formCreateModal input[name="week"]').val(weekNumb);
    }
    $('#formCreateModal input[name="created_at"]').on('change', function(){
        getWeek();
    })

    function clearValidationCreate(){
        $('#formCreateModal input').removeClass('is-invalid');
        $('span.msg').text('');
    }
    function cekValidationCreate(key, value){
        $('#formCreateModal span.'+key).text(value);
        $('#formCreateModal input[name="'+key+'"]').addClass('is-invalid');
    }
    function resetFormCreate(){
        $('#formCreateModal').trigger('reset');
    }
    $('#formCreateModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('samples.store') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                resetFormCreate();
                genSampleNumb();
                getWeek();
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area', a.status);
                }
                loader(false);
            }
        });
    });

    $('input[name="program"]').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]|[-]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    });

</script>



