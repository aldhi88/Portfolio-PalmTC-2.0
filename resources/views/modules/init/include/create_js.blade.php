<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>

    $('#finishInit').submit(function (e) {
        // loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('inits.store') }}",
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                if(a.status == 'error'){
                    showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                }
                window.location.replace(a.data.redirect);
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

    genStep1();
    function genStep1(page=null){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("inits.getStep1") }}',
            data: { page:page },
            success: function(a) {
                $('#step1').html(a);
            },
            error: (a) => {
                alert("Error #003, InitiationController-getStep1 function is invalid.");
            }
        });
    }

    genStep2();
    function genStep2(page=null){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ url("inits/getStep2") }}',
            data: { page : page },
            success: function(a) {
                $('#step2').html(a);
            },
            error: (a) => {
                alert("Error #003, InitiationController-getStep2 function is invalid.");
            }
        });
    }

    genStep3();
    function genStep3(page=null){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ url("inits/getStep3") }}',
            data: { page : page },
            success: function(a) {
                $('#step3').html(a);
            },
            error: (a) => {
                alert("Error #003, InitiationController-getStep2 function is invalid.");
            }
        });
    }
</script>





