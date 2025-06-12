<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script>


genStep1();
function genStep1(page=null){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("matur-transfers.getStep1") }}',
        data: { page:page,initId:'{{ $data["initId"] }}' },
        success: function(a) {
            $('#step1').html(a);
        },
        error: (a) => {
            alert("Error #003, LiquidTransfer-getStep1 function is invalid.");
        }
    });
}
genStep2();
function genStep2(page=null){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("matur-transfers.getStep2") }}',
        data: { page:page,initId:'{{ $data["initId"] }}' },
        success: function(a) {
            $('#step2').html(a);
        },
        error: (a) => {
            alert("Error #003, LiquidTransfer-getStep1 function is invalid.");
        }
    });
}
genStep3();
function genStep3(page=null){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("matur-transfers.getStep3") }}',
        data: { page:page,initId:'{{ $data["initId"] }}' },
        success: function(a) {
            $('#step3').html(a);
        },
        error: (a) => {
            alert("Error #003, LiquidTransfer-getStep3 function is invalid.");
        }
    });
}
genStep4();
function genStep4(page=null){
    $.ajax({
        type: 'GET', cache: false, contentType: false, processData: true,
        url: '{{ route("matur-transfers.getStep4") }}',
        data: { page:page,initId:'{{ $data["initId"] }}' },
        success: function(a) {
            $('#step4').html(a);
        },
        error: (a) => {
            alert("Error #003, LiquidTransfer-getStep4 function is invalid.");
        }
    });
}

$('#finishTransfer').submit(function (e) {
    loader(true);
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ route('matur-transfers.finishTransfer') }}",
        data: formData,
        cache: false, contentType: false, processData: false,
        success: (a) => {
            if(a.status == 'error'){
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
            }else{
                window.location.replace(a.data.redirect);
            }
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
