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
            url: '{{ route("samples.exportExcel") }}',
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
            url: '{{ route("samples.exportPDF") }}',
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
            var route = '{{ route("samples.exportPrint") }}?from='+from+'&to='+to;
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
        pageLength: 25,
        order: [
            [0, 'asc'],
        ],
        columnDefs: [
            { className: 'text-center', targets: [1] },
        ],
        ajax: '{!! route('samples.dt') !!}',
        columns: [
            { data: 'custom_name', name: 'sample_number', orderable:true, searchable:true},
            { data: 'resample_display', name: 'resample', orderable:false, searchable:false},
            { data: 'year', name: 'year', orderable:false, searchable:false},
            { data: 'month', name: 'month', orderable:false, searchable:false},
            { data: 'weekOfYear', name: 'weekOfYear', orderable:false, searchable:false},
            { data: 'master_treefile.noseleksi', name: 'master_treefile.noseleksi', orderable:true, searchable:true},
            { data: 'master_treefile.family', name: 'master_treefile.family', orderable: true },
            { data: 'master_treefile.indukbet', name: 'master_treefile.indukbet', orderable: true },
            { data: 'master_treefile.indukjan', name: 'master_treefile.indukjan', orderable: true },
            { data: 'master_treefile.blok', name: 'master_treefile.blok', orderable: true },
            { data: 'master_treefile.baris', name: 'master_treefile.baris', orderable: true },
            { data: 'master_treefile.pokok', name: 'master_treefile.pokok', orderable: true },
            { data: 'master_treefile.tahuntanam', name: 'master_treefile.tahuntanam', orderable: true },
            { data: 'master_treefile.tipe', name: 'master_treefile.tipe', orderable: true },
            { data: 'program', name: 'program', orderable: true },
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
</script>
