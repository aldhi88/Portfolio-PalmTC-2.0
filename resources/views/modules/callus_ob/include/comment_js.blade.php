<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/moment.js/moment.min.js')}}"></script>
<script>
    var dtComment = $('#DTComment').DataTable({
        processing: true,serverSide: true,pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: {
            url: '{{ route("field-lists.dtComment") }}',
            data: function(d){ // note !
                d.id = '{{$data["initId"]}}';
            }
        },
        columns: [
            { data: 'action', name: 'created_at', orderable: true, searchable:false },
            { data: 'created_at_format', name: 'created_at', orderable: true, searchable:false },
            { data: 'comment', name: 'comment', orderable: false, searchable:true },
            { data: 'image_file', name: 'file', orderable: false, searchable:true },
            { data: 'image_format', name: 'image', orderable: false, searchable:false },
        ],
        initComplete: function(settings){
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

    $("#deleteCommentModal").on("show.bs.modal", function(e) {
        let button = $(e.relatedTarget);
        let jsonData = button.attr("data-json");
        let firstParse = JSON.parse(jsonData);
        let data = JSON.parse(firstParse);
        $('p.comment').text(data.comment);
        $('input[name="id"]').val(data.id);
    });
</script>

