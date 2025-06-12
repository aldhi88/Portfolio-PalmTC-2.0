<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script>
    $("body").on("click", "button#btnExcel", function(){
        loader(true);
        var from = parseInt($("select[name='from_year']").val());
        var to = parseInt($("select[name='to_year']").val());
        if(from > to){
            showAlert('danger', 'times', 'alert-area-print', 'Year value is not valid.');
            return false;
        }

        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("callus-lists.exportExcel") }}',
            data:{
                from: from,
                to: to,
            },
            success: (a) => {
                var link = document.createElement('a');
                link.href = a.data.link +'/'+ a.data.filename;
                link.download = a.data.filename;
                link.dispatchEvent(new MouseEvent('click'));
                loader(false);
            },
            error: (a) => {
                alert("Error #003, exportExcel.");
            }
        });
    })

    $("body").on("click", "button#btnPdf", function(){
        loader(true);
        var from = parseInt($("select[name='from_year']").val());
        var to = parseInt($("select[name='to_year']").val());
        if(from > to){
            showAlert('danger', 'times', 'alert-area-print', 'Year value is not valid.');
            return false;
        }

        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ route("callus-lists.exportPDF") }}',
            data:{
                from: from,
                to: to,
            },
            success: (a) => {
                var link = document.createElement('a');
                link.href = a.data.link +'/'+ a.data.filename;
                link.download = a.data.filename;
                link.dispatchEvent(new MouseEvent('click'));
                loader(false);
            },
            error: (a) => {
                alert("Error #003, exportPDF.");
            }
        });
    })

    $("body").on("click", "button#btnPrint", function(){
        loader(true);
        var from = parseInt($("select[name='from_year']").val());
        var to = parseInt($("select[name='to_year']").val());
        if(from > to){
            showAlert('danger', 'times', 'alert-area-print', 'Year value is not valid.');
            return false;
        }

        else{
            var div = document.getElementById("exportPrint");
            var route = '{{ route("callus-lists.exportPrint") }}?from='+from+'&to='+to;
            div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
        }
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }

    // datatables
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 50,
        order: [
            [0, 'asc'],
        ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("callus-lists.dt") }}',
        columns: [
            { data: 'tc_samples.sample_number_display', name: 'tc_samples.sample_number', orderable:false, searchable:true},
            { data: 'created_at_format', name: 'created_at', orderable:false, searchable:false},
            { data: 'total_explant', name: 'total_explant', orderable:false, searchable:false},
            { data: 'total_explant_callus', name: 'total_explant_callus', orderable:false, searchable:false},
            { data: 'persen_explant_callus', name: 'persen_explant_callus', orderable:false, searchable:false},
            { data: 'total_bottle_callus', name: 'total_bottle_callus', orderable:false, searchable:false},
            { data: 'tc_samples.master_treefile.tipe', name: 'tc_samples.master_treefile.tipe', orderable:false, searchable:false},
            { data: 'tc_samples.program', name: 'tc_samples.program', orderable:false, searchable:false},
            { data: 'end_date', name: 'date_stop', orderable:false, searchable:false},
        ]
    });
</script>
